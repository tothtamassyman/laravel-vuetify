import {PureAbility} from '@casl/ability';
import { useAuthStore } from '@/stores/authStore.js';
import axios from "@/plugins/axios.js";

export const ability = new PureAbility([]);

/**
 * Update abilities in the ability instance.
 *
 * @param {Array} abilities - Array of abilities
 * @returns {void}
 */
export function updateAbilities(abilities) {
    ability.update(abilities);
}

/**
 * Fetch abilities from the API and update the ability instance.
 *
 * @returns {Promise<void>}
 */
export async function fetchAndSetAbilities() {
    try {
        const response = await axios.get('/abilities');
        const abilities = response.data.abilities || [];
        updateAbilities(abilities);

        const authStore = useAuthStore();
        await authStore.fetchAbilities();
    } catch (error) {
        console.error('Failed to fetch abilities:', error);
        throw error;
    }
}

export default ability;
