<script>
import MisidentifiedPatients from './MisidentifiedPatients.vue';

export default {
    extends: MisidentifiedPatients,
    props: {
        account: {
            default: null
        }
    }
};
</script>
