import manifest from '@neos-project/neos-ui-extensibility';
import { HealthCheckView } from './Components/HealthCheckView';

manifest('Shel.Neos.HealthCheck:Plugin', {}, (globalRegistry) => {
    const containerRegistry = globalRegistry.get('containers');
    containerRegistry.set('PrimaryToolbar/Left/Shel.Neos.HealthCheck.View', HealthCheckView);
});
