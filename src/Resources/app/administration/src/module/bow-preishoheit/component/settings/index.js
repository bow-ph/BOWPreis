import template from './settings.html.twig';

export default {
    template,

    props: {
        product: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            isLoading: false
        };
    },

    methods: {
        onSave() {
            this.$emit('save');
        }
    }
};
