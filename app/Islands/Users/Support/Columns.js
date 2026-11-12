export const USER_COLUMN_KEYS = ['name', 'email', 'status', 'last_login_at', 'created_at', 'actions'];

export function userColumns(t) {
    return [
        { key: 'name', label: t('Name'), sort: 'name', default: true, locked: true },
        { key: 'email', label: t('Email'), sort: 'email', default: true },
        { key: 'status', label: t('Status'), default: true },
        { key: 'last_login_at', label: t('Last login'), sort: 'last_login_at', default: true },
        { key: 'created_at', label: t('Joined'), sort: 'created_at', default: true },
        { key: 'actions', label: t('Actions'), default: true, locked: true },
    ];
}
