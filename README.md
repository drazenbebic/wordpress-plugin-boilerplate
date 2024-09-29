# Getting Started

Let's say you want to create a plugin called "Gutenberg Media Blocks". From its
name we derive the following information:

- **Name:** `Gutenberg Media Blocks`
- **Slug:** `gutenberg-media-blocks`
- **Abbreviation:** `GMB` and `gmb`

Now we need to do the following:

1. Adjust the main plugin file (Name, URI, Author, etc.)
2. Rename the plugin directory and main plugin file, i.e.
`gutenberg-media-blocks/gutenberg-media-blocks.php`
3. Search and replace all abbreviations, i.e. `WPPB` with `GMB` and `wppb` with
`gmb`
4. Adjust the `package.json` & `composer.json`
5. Adjust the PSR-4 autoloader namespace in the composer.json
6. Adjust the `.releaserc.json` if you want to use Semantic Release
7. Adjust the `phpcs.xml`