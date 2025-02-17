import './page/bow-preishoheit-list';
import './page/bow-preishoheit-detail';
import './component/settings';
import './component/history';
import './component/errors';

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
                parentPath: 'sw.catalogue.index'
            }
        },
        detail: {
            component: 'bow-preishoheit-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'bow.preishoheit.list'
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
                        parentPath: 'bow.preishoheit.detail'
                    }
                },
                history: {
                    component: 'bow-preishoheit-history',
                    path: 'history',
                    meta: {
                        parentPath: 'bow.preishoheit.detail'
                    }
                },
                errors: {
                    component: 'bow-preishoheit-errors',
                    path: 'errors',
                    meta: {
                        parentPath: 'bow.preishoheit.detail'
                    }
                }
            }
        }
    },

    navigation: [{
        label: 'bow-preishoheit.general.mainMenuItemGeneral',
        color: '#ff3d58',
        path: 'bow.preishoheit.list',
        icon: 'regular-shopping-bag',
        parent: 'sw-catalogue',
        position: 100
    }]
});
