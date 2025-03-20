import './page/bow-preishoheit-list';
import './page/bow-preishoheit-job-detail';
import './page/bow-preishoheit-job-list';
import './page/bow-preishoheit-job-create';

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
        jobList: {
            component: 'bow-preishoheit-job-list',
            path: 'jobs',
            meta: {
                parentPath: 'bow.preishoheit.list',
                privilege: 'bow_preishoheit.viewer'
            }
        },
        jobDetail: {
            component: 'bow-preishoheit-job-detail',
            path: 'jobs/detail/:jobId',
            meta: {
                parentPath: 'bow.preishoheit.jobList',
                privilege: 'bow_preishoheit.viewer'
            },
            props: {
                default(route) {
                    return { jobId: route.params.jobId };
                }
            }
        },
        jobCreate: {
            component: 'bow-preishoheit-job-create',
            path: 'jobs/create',
            meta: {
                parentPath: 'bow.preishoheit.jobList',
                privilege: 'bow_preishoheit.editor'
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
        label: 'bow-preishoheit.jobs.menuLabel',
        color: '#ff3d58',
        path: 'bow.preishoheit.jobList',
        icon: 'regular-list',
        parent: 'sw-catalogue',
        position: 120,
        privilege: 'bow_preishoheit.viewer'
    }]
});
