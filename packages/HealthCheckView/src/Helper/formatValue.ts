export default function formatValue(value: any): string {
    if (typeof value === 'string') {
        return value;
    }

    if (typeof value === 'number') {
        return value.toString();
    }

    if (Array.isArray(value)) {
        return `[${value.map(formatValue).join(', ')}]`;
    }

    if (typeof value === 'object' && value !== null) {
        // Pretty print JSON objects recursively
        let output = '';
        Object.keys(value).forEach((key) => {
            if (typeof value[key] === 'object' && value[key] !== null) {
                value[key] = formatValue(value[key]);
            } else if (typeof value[key] === 'string') {
                value[key] = value[key].replace(/\\n/g, '\n');
            } else if (typeof value[key] === 'number') {
                value[key] = value[key].toString();
            }
            output += `${key}: ${value[key]}\n`;
        });
        value = output.trim();
    }

    return String(value);
}
