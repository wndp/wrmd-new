import {usePage} from '@inertiajs/vue3';

export function active(slug)
{
    return usePage().props.activatedExtensions.includes(slug);
}
