<template>
  <div class="restore-form">
    <div class="progress pane" v-if="this.status === 'loading'">
      <div class="spinner"></div>

      <div class="progress-text">
        Restoring...
      </div>
    </div>

    <div class="result pane" v-if="this.status === 'finished' || this.status === 'error'">
      <div class="" v-if="this.status === 'finished'">
        <p>
          <span class="checkmark-icon"></span>

          <span class="progress-text">
            Done!
          </span>
        </p>
      </div>

      <code v-else>
        {{ this.result }}
      </code>
    </div>

    <button
      @click="startDbRestore"
      class="btn submit"
      :class="this.status === 'loading' ? 'disabled' : ''"
      :disabled="this.status === 'loading'"
      v-if="this.propKey"
    >
      Restore Database
    </button>

    <div class="btn submit disabled" disabled="disabled" v-else>
      Craft Report API Key is missing!
    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  import { axiosConfiguration } from '../js/utils.js';

  export default {
    name: 'RestoreForm',
    props: {
      propActionUrl: {
        default: '',
        type: String
      },
      propCsrfToken: {
        default: '',
        type: String
      },
      propKey: {
        default: '',
        type: String
      }
    },
    computed: {
      result() {
        return this.response.message ?? this.response.statusText ?? '';
      },
      status() {
        if (this.loading === true) {
          return 'loading';
        }

        if (this.result === 'OK') {
          return 'finished';
        }

        if (this.result) {
          return 'error';
        }

        return 'ready';
      }
    },
    data() {
      return {
        loading: false,
        response: {}
      }
    },
    methods: {
      startDbRestore() {
        if (!confirm('Are you sure want to restore your last backup from Craft Report? This will destroy your current database.')) {
          return;
        }

        const api = axios.create(axiosConfiguration(this.propActionUrl));

        this.loading = true;

        api.post('', {
          csrfToken: this.propCsrfToken
        }).then(response => {
          this.response = response;
          this.loading = false;
        }).catch(error => {
          this.response = error;
          this.loading = false;
        });
      }
    },
  };
</script>

<style>
  .progress,
  .result {
    margin-top: 2rem;
    margin-bottom: 2rem;
  }

  .progress {
    display: flex;
    align-items: center;
    justify-content: start;
  }

  .progress-text {
    margin-left: 1rem;
    font-weight: bold;
  }
</style>
