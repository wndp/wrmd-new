import js from "@eslint/js";
import pluginVue from 'eslint-plugin-vue'

export default [
    js.configs.recommended,
    ...pluginVue.configs['flat/recommended'],
    {
        rules: {
            'vue/no-unused-vars': 'error',
            'no-console': 'error',
            "vue/attribute-hyphenation": ["error", "never", {
                "ignore": []
            }],
            "vue/first-attribute-linebreak": ["error", {
                "singleline": "ignore",
                "multiline": "below"
            }],
            'vue/no-mutating-props': ['error', {
                'shallowOnly': true
            }]
        }
    }
];
