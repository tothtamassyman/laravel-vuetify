import axios from 'axios';

const getToken = () => {
    const token = localStorage.getItem('token');
    return token !== 'undefined' ? token : null;
};

const logErrorInDevelopment = (error, context = 'Error') => {
    if (import.meta.env.MODE === 'development') {
        console.error(`[${context}]`, error);
    }
};

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_APP_API_URL || 'http://localhost:8000/api',
    timeout: 10000,
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

const defaultStatusMessages = {
    422: "Please check the form for errors.",
    429: "Too many requests. Please try again later.",
    403: "You're not allowed to do that.",
    401: "Unauthorized. Please log in again.",
    500: "Internal Server Error.",
    default: "Something went wrong. Please try again later.",
};

const defaultCodeMessages = {
    ECONNABORTED: "Request timed out. Please try again.",
    ERR_NETWORK: "Couldn't connect to the server. Please check your internet connection.",
    default: "An unexpected network error occurred.",
};

axiosInstance.interceptors.request.use(
    (config) => {
        const token = getToken();
        if (token && /^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/.test(token)) {
            config.headers['Authorization'] = `Bearer ${token}`;
        } else {
            console.warn('No valid token found.');
        }

        if (!config.method || typeof config.method !== 'string') {
            return Promise.reject(new Error('Invalid or missing HTTP method'));
        }

        if (!config.url) {
            return Promise.reject(new Error('Missing URL in request'));
        }

        if (!config.url.startsWith('http') && !config.baseURL) {
            return Promise.reject(new Error('Invalid or missing baseURL'));
        }

        return config;
    },
    (error) => handleRequestError(error)
);

const handleResponse = (response) => {
    if (!response.data || typeof response.data !== 'object') {
        console.warn('Unexpected response format:', response);
    }
    return response;
};

const handleRequestError = (error) => {
    if (!error.config) {
        console.error('Request configuration error:', error);
        error.message = "Invalid request configuration. Please check your setup.";
    }

    logErrorInDevelopment(error, 'Request');

    return Promise.reject(error);
};

const handleResponseError = (error) => {
    const message = error.response?.data?.message || defaultStatusMessages.default;
    const status = error.response?.status || null;
    const errors = error.response?.data?.errors || {};

    if (error.code) {
        switch (error.code) {
            case 'ECONNABORTED':
                error.message = defaultCodeMessages[error.code];
                break;

            case 'ERR_NETWORK':
                error.message = defaultCodeMessages[error.code];
                break;

            default:
                error.message = defaultCodeMessages.default;
        }
    }

    if (status) {
        switch (status) {
            case 422:
                error.backendErrors = {};
                for (let field in errors) {
                    if (errors[field] && Array.isArray(errors[field])) {
                        error.backendErrors[field] = errors[field][0];
                    }
                }
                error.message = defaultStatusMessages[status];
                break;
            case 429:
                error.message = defaultStatusMessages[status];
                break;

            case 403:
                error.message = defaultStatusMessages[status];
                break;

            case 401:
                localStorage.removeItem('token');
                if (window.location.pathname !== '/login') {
                    window.location.href = '/login';
                }
                error.message = defaultStatusMessages[status];
                break;

            case 500:
                if (message.includes("SQLSTATE[HY000] [2002]")) {
                    error.message = "Couldn't connect to Database. Please try again later.";
                } else if (message.includes("Error Message : ORA-00001: unique constraint")) {
                    error.message = "The specified data already exists.";
                } else if (message.includes("oci_connect(): ORA-12170:")) {
                    error.message = "Couldn't connect to Database. Please try again later.";
                } else if (message.includes("oci_connect(): ORA-12545:")) {
                    error.message = "Couldn't connect to Database. Please try again later.";
                } else {
                    error.message = defaultStatusMessages[status];
                }
                break;

            default:
                error.message = defaultStatusMessages.default;
        }
    }

    if (!error.code && !status) {
        error.message = "An unknown error occurred. Please try again.";
    }

    logErrorInDevelopment(error, 'Response');

    return Promise.reject(error);
};

axiosInstance.interceptors.response.use(handleResponse, handleResponseError);

export default axiosInstance;
