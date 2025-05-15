export async function fetchData<T = any>(endpoint: string, params?: Record<string, any>, method = 'GET'): Promise<T> {
    if (params && method === 'GET') {
        endpoint = Object.keys(params).reduce((url, key) => {
            return url + '&' + key + '=' + encodeURIComponent(params[key]);
        }, endpoint + '?');
    }
    return fetch(endpoint, {
        method,
        credentials: 'include',
        body: params && method === 'POST' ? JSON.stringify(params) : undefined,
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
    }).then((response: Response) => {
        if (response.status !== 200) {
            throw new Error(`The request failed with code ${response.status}: "${response.statusText}"`);
        }
        if (response.headers.get('Content-Type')?.includes('application/json')) {
            return response.json();
        }
        return response.text();
    }) as Promise<T>;
}
