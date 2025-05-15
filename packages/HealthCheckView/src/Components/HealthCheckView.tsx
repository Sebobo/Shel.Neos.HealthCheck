import React, { PureComponent } from 'react';
import { Icon } from '@neos-project/react-ui-components';

import './HealthCheckView.css';
import { loadEnvironments } from '../Helper/endpoints';
import { classnames } from '../Helper/classnames';
import formatValue from '../Helper/formatValue';

export class HealthCheckView extends PureComponent {
    state = {
        loading: true,
        environments: {} as HealthCheckEnvironments,
    };

    componentDidMount() {
        loadEnvironments()
            .then(({ success, message, environments }) => {
                this.setState({ loading: false, environments });
            })
            .catch((err) => {
                this.setState({ loading: false });
            });
    }

    render() {
        const { environments } = this.state;

        return (
            <div className="health-check">
                {Object.keys(environments).length > 0 ? (
                    Object.keys(environments)
                        .filter((environment) => environments[environment].data.status !== 'unavailable')
                        .sort((a, b) => (environments[a].isCurrent ? -1 : 1))
                        .map((environment) => {
                            const { label, icon, isCurrent, data } = environments[environment];
                            return (
                                <details
                                    className={classnames(
                                        'health-check__item',
                                        isCurrent && 'health-check__item--current'
                                    )}
                                    name="health-check__items"
                                    key={label}
                                >
                                    <summary>
                                        {icon ? <Icon icon={icon} padded="right" /> : ''}
                                        {label}
                                    </summary>
                                    <table>
                                        {Object.keys(data).map((key) => {
                                            const formattedValue = formatValue(data[key]);
                                            return (
                                                <tr key={key}>
                                                    <td>{key.charAt(0).toUpperCase() + key.slice(1)}</td>
                                                    <td>
                                                        <pre title={formattedValue}>{formattedValue}</pre>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </table>
                                </details>
                            );
                        })
                ) : (
                    <div className="health-check__item health-check__item--loading">Loading environments…</div>
                )}
            </div>
        );
    }
}
