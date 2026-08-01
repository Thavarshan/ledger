const [major, minor] = process.versions.node.split('.').map(Number);

const supported =
    (major === 20 && minor >= 19) ||
    major >= 23 ||
    (major === 22 && minor >= 12);

if (!supported) {
    console.error(
        'Vite requires Node ^20.19.0 or >=22.12.0. Run `nvm use` to use the version in .nvmrc.',
    );
    process.exit(1);
}
