export function __(key, replacements = {}) {
    let translation = window.i18n[key] || key;

    Object.keys(replacements).forEach(r => {
      translation = translation.replace(`:${r}`, replacements[r]);
    });

    return translation;
}
