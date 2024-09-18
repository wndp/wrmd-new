import {usePage} from '@inertiajs/vue3';

export default function ExtensionApi() {
    const navigation = (key) => usePage().props.extensions[key] || []

    return {
        navigation
    }
}
