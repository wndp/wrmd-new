import {usePage} from '@inertiajs/vue3';

export function can(ability)
{
    return usePage().props.auth.abilities.includes(ability);
}



