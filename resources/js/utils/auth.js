import { usePage } from '@inertiajs/vue3';

const page = usePage();

export const userHasPermission = (permissions) => {
    if (!page.props.auth || !page.props.auth.user || !page.props.auth.user.permissions) {
        if (typeof window !== 'undefined') {
            window.location.href = '/login';
        }
        return false;
    }

    if (Array.isArray(permissions)) {
        return permissions.some(permission => page.props.auth.user.permissions.includes(permission));
    }
    return page.props.auth.user.permissions.includes(permissions);
};

export const isActiveLink = (basePath) => {
    const currentPath = page.url;
    return currentPath === basePath || currentPath.startsWith(basePath + "/");
};
