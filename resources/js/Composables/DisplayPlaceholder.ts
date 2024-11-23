export function coalesce(value: string|number|null)
{
    return value ?? '---';
}
