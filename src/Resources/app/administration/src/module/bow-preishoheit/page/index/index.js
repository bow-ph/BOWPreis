import template from './index.html.twig';
import { Component } from 'src/core/shopware';

// Komponenten für Tabs importieren
import './component/general';
import './component/jobs';
import './component/results';
import './component/mapping';
import './component/settings';

Component.register('bow-preishoheit-index', {
    template,

    data() {
        return {
            activeTab: 'general'
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle('Preishoheit Integration')
        };
    }
});
