import { fetchData } from './fetch';

export async function loadEnvironments() {
    return fetchData<{
        success: boolean;
        message: string;
        environments: HealthCheckEnvironments;
    }>('/neos/service/data-source/shel-neos-healthcheck-environments');
}
