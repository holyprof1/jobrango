const fs = require('fs')
const path = require('path')

const nestedWebpackBarPath = path.join(
    process.cwd(),
    'node_modules',
    'laravel-mix',
    'node_modules',
    'webpackbar'
)

if (! fs.existsSync(nestedWebpackBarPath)) {
    process.exit(0)
}

fs.rmSync(nestedWebpackBarPath, { recursive: true, force: true })
