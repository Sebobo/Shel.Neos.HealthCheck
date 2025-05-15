type HealthCheckEnvironments = {
    [key: string]: {
        label: string;
        icon?: string;
        data: {
            status: 'error' | 'warning' | 'ok' | 'unavailable';
            message: string;
            timestamp: number;
            [key: string]: any;
        };
        isCurrent: boolean;
        isAvailable: boolean;
    };
};
