<template>
    <div>
        <heading class="mb-3">{{ __('ResetPassword') }}</heading>

        <div class="space-y-4">
            <card class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="flex flex-col md:flex-row">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                        <label for="old_password" class="inline-block pt-2 leading-tight space-x-1">
                            <span>{{ __('Current password') }}</span>
                            <span class="text-danger text-sm">*</span>
                        </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 md:w-3/5 w-full md:py-5">
                        <div class="space-y-1">
                            <input
                                v-model="current_password"
                                id="old_password" type="password"
                                :class="{'form-input-border-error': errors.filter((el) => el['key'] === 'current_password').length}"
                                class="w-full form-control form-input form-input-bordered"
                                name="current_password">

                            <p class="help-text mt-2 help-text-error"
                               v-if="errors.filter((el) => el['key'] === 'current_password').length">
                                {{ errors.filter((el) => el['key'] === 'current_password')[0].message }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                        <label for="new_password" class="inline-block pt-2 leading-tight space-x-1">
                            <span>{{ __('New password') }}</span>
                            <span class="text-danger text-sm">*</span>
                        </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 md:w-3/5 w-full md:py-5">
                        <div class="space-y-1">
                            <input
                                v-model="new_password"
                                id="new_password" type="password"
                                :class="{'form-input-border-error': errors.filter((el) => el['key'] === 'new_password').length}"
                                class="w-full form-control form-input form-input-bordered"
                                name="new_password">
                            <password-meter :password="new_password"/>
                            <p class="help-text mt-2 help-text-error"
                               v-if="errors.filter((el) => el['key'] === 'new_password').length">
                                {{ errors.filter((el) => el['key'] === 'new_password')[0].message }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                        <label for="confirm_password" class="inline-block pt-2 leading-tight space-x-1">
                            <span>{{ __('Confirm password') }}</span>
                            <span class="text-danger text-sm">*</span>
                        </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 md:w-3/5 w-full md:py-5">
                        <div class="space-y-1">
                            <input
                                v-model="confirm_new_password"
                                id="confirm_password" type="password"
                                :class="{'form-input-border-error': errors.filter((el) => el['key'] === 'confirm_new_password').length}"
                                class="w-full form-control form-input form-input-bordered"
                                name="confirm_new_password">
                            <password-meter :password="confirm_new_password"/>
                            <p class="help-text mt-2 help-text-error"
                               v-if="errors.filter((el) => el['key'] === 'confirm_new_password').length">
                                {{ errors.filter((el) => el['key'] === 'confirm_new_password')[0].message }}
                            </p>
                        </div>
                    </div>
                </div>
            </card>
        </div>

        <div
            class="flex flex-col md:flex-row md:items-center justify-center md:justify-end space-y-2 md:space-y-0 space-x-3">
            <div class="mt-3 py-2">
                <LoadingButton align="center" @click="submitForm()" type="button"
                               class="btn btn-default btn-primary inline-flex items-center relative">
                    <span>{{ __('Save Password') }}</span>
                </LoadingButton>
            </div>
        </div>
    </div>
</template>

<script>
import passwordMeter from "vue-simple-password-meter";

export default {
    components: {passwordMeter},
    data() {
        return {
            errors: [],
            current_password: null,
            new_password: null,
            confirm_new_password: null,
            min_password_size: null
        }
    },
    mounted() {
        this.getPasswordSize()
    },
    methods: {
        getPasswordSize: function () {
            Nova.request().get('/nova-vendor/nova-password-reset/min-password-size').then(response => {
                    this.min_password_size = response.data.minpassw;
                }
            );
        },
        checkForm: function () {
            this.errors = [];

            if (!this.current_password) {
                this.errors.push({key: 'current_password', message: this.__('novaPasswordReset.oldPasswordRequired')});
            }

            if (!this.new_password) {
                this.errors.push({key: 'new_password', message: this.__('novaPasswordReset.newPasswordRequired')});
            }

            if (this.new_password && this.new_password.length < this.min_password_size) {
                this.errors.push({
                    key: 'new_password',
                    message: this.__('novaPasswordReset.newPasswordSize', {minpass: this.min_password_size})
                });
            }

            if (!this.confirm_new_password) {
                this.errors.push({
                    key: 'confirm_new_password',
                    message: this.__('novaPasswordReset.confirmPasswordRequired')
                });
            }

            if (this.confirm_new_password && this.confirm_new_password.length < this.min_password_size) {
                this.errors.push({
                    key: 'confirm_new_password',
                    message: this.__('novaPasswordReset.confirmPasswordSize', {minpass: this.min_password_size})
                });
            }
        },
        submitForm: function () {
            this.checkForm();

            if (this.errors.length > 0)
                return;

            Nova.request().post('/nova-vendor/nova-password-reset/reset-password', _.tap(new FormData(), formData => {
                formData.append('current_password', this.current_password)
                formData.append('new_password', this.new_password)
                formData.append('confirm_new_password', this.confirm_new_password)
            })).then(_ => {
                Nova.$toasted.show(this.__('novaPasswordReset.success'), {type: 'success'})
            }).catch(reason => {
                if (reason.response.data && reason.response.data.errors) {
                    let errors = reason.response.data.errors;
                    for (const key in errors) {
                        for (const err in errors[key]) {
                            this.errors.push({key: key, message: errors[key][err]});

                            Nova.$toasted.show(errors[key][err], {type: 'error'})
                        }
                    }
                }
            })
        }
    }
}
</script>
