export function globalErrorHandler(error) {
    console.error('Routing error:', error);

    if (import.meta.env.MODE === 'development') {
        alert('An error occurred during navigation. Check the console for details.');
    }
}
