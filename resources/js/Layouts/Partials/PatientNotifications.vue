<script>
  import delay from 'lodash/delay';
  import { ref } from 'vue';

  export default {
    props: {
      patientId: {
        type: String,
        required: true
      }
    },
    setup() {
      const timeoutID = ref();

      return {
        timeoutID
      }
    },
    mounted() {
      window.Echo.private(`patient.${this.patientId}`)
        .listen('NotifyPatient', (e) => {
          this.$notify(e, 15000);
        });

      this.timeoutID = delay(() => {
        window.axios.get(this.route('patients.notifications', this.patientId));
      }, 2000)
    },
    unmounted() {
      window.Echo.leave(`patient.${this.patientId}`);
      clearTimeout(this.timeoutID)
    },
    render() {
      return null
    },
  };
</script>
