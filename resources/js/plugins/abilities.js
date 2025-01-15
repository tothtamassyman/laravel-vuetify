import {PureAbility} from '@casl/ability';

export const ability = new PureAbility([
    { action: 'view', subject: 'dashboard link' },
    { action: 'view', subject: 'settings link' },
    { action: 'view', subject: 'own-profile link' },
    { action: 'view', subject: 'access-management link' },
    { action: 'view', subject: 'groups link' },
    { action: 'view', subject: 'users link' },
    { action: 'view', subject: 'roles link' },
    { action: 'view', subject: 'permissions link' },
]);

export default ability;
