export function classnames(...args: (string | boolean | null | undefined)[]): string {
    return args
        .filter((arg) => typeof arg === 'string' || (typeof arg === 'boolean' && arg))
        .join(' ')
        .trim();
}
