import { readFileSync, writeFileSync, existsSync } from 'node:fs';
import { createRequire } from 'node:module';
import { resolve } from 'node:path';

const require = createRequire(import.meta.url);
const Converter = require('openapi-to-postmanv2');
const specification = resolve('docs/api/openapi.yaml');
const collectionPath = resolve('docs/api/postman/ledger-v1.postman_collection.json');
const checkOnly = process.argv.includes('--check');

const converted = await new Promise((resolvePromise, reject) => {
    Converter.convert(
        { type: 'file', data: specification },
        { folderStrategy: 'Tags', schemaFaker: false },
        (error, result) => {
            if (error || !result?.result) {
                reject(error ?? new Error('Unable to convert the OpenAPI document.'));

                return;
            }

            resolvePromise(result.output[0].data);
        },
    );
});

const collection = typeof converted === 'string' ? JSON.parse(converted) : converted;
const normalizeBody = (value) => {
    if (Array.isArray(value)) {
        return value.map(normalizeBody);
    }

    if (value && typeof value === 'object') {
        return Object.fromEntries(Object.entries(value).map(([key, child]) => [key, normalizeBody(child)]));
    }

    if (typeof value === 'number') {
        return 0;
    }

    if (typeof value === 'boolean') {
        return false;
    }

    return typeof value === 'string' ? 'string' : value;
};

const removeGeneratedIds = (value) => {
    if (Array.isArray(value)) {
        value.forEach((child) => removeGeneratedIds(child));

        return;
    }

    if (value && typeof value === 'object') {
        delete value.id;
        delete value._postman_id;
        delete value.response;

        if (value.key === 'Location') {
            value.value = '{{base_url}}';
        }

        if (typeof value.body === 'string') {
            try {
                value.body = JSON.stringify(normalizeBody(JSON.parse(value.body)), null, 2);
            } catch {
                value.body = 'string';
            }
        }

        Object.values(value).forEach(removeGeneratedIds);
    }
};

removeGeneratedIds(collection);
collection.info.name = 'Ledger API v1';
collection.variable = [
    { key: 'base_url', value: 'http://localhost:8000/api/v1' },
    { key: 'token', value: '' },
    { key: 'account_id', value: '' },
    { key: 'transaction_id', value: '' },
    { key: 'idempotency_key', value: '' },
];

const output = JSON.stringify(collection, null, 2)
    .replaceAll('{{baseUrl}}', '{{base_url}}')
    .replaceAll('{{bearerToken}}', '{{token}}')
    + '\n';

if (checkOnly) {
    if (!existsSync(collectionPath) || readFileSync(collectionPath, 'utf8') !== output) {
        console.error('Postman collection is out of date. Run npm run api:postman.');
        process.exit(1);
    }

    process.exit(0);
}

writeFileSync(collectionPath, output);
