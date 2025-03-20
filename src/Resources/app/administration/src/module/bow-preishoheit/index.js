import './page/bow-preishoheit-list';
import './page/bow-preishoheit-detail';
import './component/settings';
import './component/history';
import './component/errors';
import './component/product-grid';
import './page/bow-preishoheit-preview';

Shopware.Module.register('bow-preishoheit', {
    type: 'plugin',
    name: 'bow-preishoheit.general.mainMenuItemGeneral',
    title: 'bow-preishoheit.general.mainMenuItemGeneral',
    description: 'bow-preishoheit.general.descriptionTextModule',
    color: '#ff3d58',
    icon: 'regular-shopping-bag',

    routes: {
        list: {
            component: 'bow-preishoheit-list',
            path: 'list',
            meta: {
                parentPath: 'sw.catalogue.index',
                privilege: 'bow_preishoheit.viewer'
            }
        },
        detail: {
            component: 'bow-preishoheit-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'bow.preishoheit.list',
                privilege: 'bow_preishoheit.viewer'
            },
            props: {
                default(route) {
                    return { productId: route.params.id };
                }
            },
            children: {
                settings: {
                    component: 'bow-preishoheit-settings',
                    path: 'settings',
                    meta: {
                        parentPath: 'bow.preishoheit.detail',
                        privilege: 'bow_preishoheit.editor'
                    }
                },
                history: {
                    component: 'bow-preishoheit-history',
                    path: 'history',
                    meta: {
                        parentPath: 'bow.preishoheit.detail',
                        privilege: 'bow_preishoheit.viewer'
                    }
                },
                errors: {
                    component: 'bow-preishoheit-errors',
                    path: 'errors',
                    meta: {
                        parentPath: 'bow.preishoheit.detail',
                        privilege: 'bow_preishoheit.viewer'
                    }
                }
            }
        },
        preview: {
            component: 'bow-preishoheit-preview',
            path: 'preview',
            meta: {
                parentPath: 'sw.catalogue.index',
                privilege: 'bow_preishoheit.viewer'
            }
        }
    },

    acl: {
        viewer: {
            privileges: [
                'product:read',
                'bow_preishoheit_product:read',
                'bow_preishoheit_error_log:read'
            ],
            dependencies: []
        },
        editor: {
            privileges: [
                'product:update',
                'bow_preishoheit_product:update',
                'bow_preishoheit_product:create',
                'system_config:read',
                'system_config:update'
            ],
            dependencies: ['viewer']
        }
    },

    navigation: [{
        label: 'bow-preishoheit.general.mainMenuItemGeneral',
        color: '#ff3d58',
        path: 'bow.preishoheit.list',
        icon: 'regular-shopping-bag',
        parent: 'sw-catalogue',
        position: 100,
        privilege: 'bow_preishoheit.viewer'
    }, {
        label: 'bow-preishoheit.general.preview',
        color: '#ff3d58',
        path: 'bow.preishoheit.preview',
        icon: 'regular-eye',
        parent: 'sw-catalogue',
        position: 110,
        privilege: 'bow_preishoheit.viewer'
    }]
});