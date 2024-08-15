import { FlatCompat } from '@eslint/eslintrc';

const compat = new FlatCompat({
    // This should be the path to your project's package.json
    baseDirectory: process.cwd(),
});

export default [
    // Apply recommended settings from @wordpress/eslint-plugin
    ...compat.extends('plugin:@wordpress/eslint-plugin/recommended'),

    // Specify your ignore patterns
    {
        ignores: [],
    },
];
