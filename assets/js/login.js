(function ($) {

    $(window).load(function () {
        stm_lms_login(true);
    });

})(jQuery);

function stm_lms_login(redirect) {
    var $ = jQuery;
    $('.stm-lms-login:not(.loaded)').each(function(){
        $(this).addClass('loaded');
        var vue_obj = {
            el: this,
            data: function () {
                return {
                    loading: false,
                    login: '',
                    password: '',
                    message: '',
                    status: '',
                    recaptcha: '',
                    captcha_error: ''
                }
            },
            methods: {
                logIn() {
                    var vm = this;
                    vm.loading = true;
                    vm.message = '';
                    var data = {
                        'user_login' : vm.login,
                        'user_password' : vm.password,
                        'recaptcha' : vm.recaptcha,
                    };
                    this.$http.post(stm_lms_ajaxurl + '?action=stm_lms_login', data).then(function(response){
                        vm.message = response.body['message'];
                        vm.status = response.body['status'];
                        vm.loading = false;

                        if (response.body['user_page']) {
                            if (redirect) {
                                window.location = response.body['user_page'];
                            } else {
                                location.reload();
                            }
                        }

                        if(typeof VueRecaptcha !== 'undefined') this.$refs.recaptcha.reset();
                    });
                },
                onCaptchaVerified(recaptchaToken) {
                    this.recaptcha = recaptchaToken;
                    this.captcha_error = false;
                },
                onCaptchaExpired() {
                    this.recaptcha = '';
                }
            }
        };

        if(typeof VueRecaptcha !== 'undefined') {
            vue_obj['components'] = {VueRecaptcha};
        }

        new Vue(vue_obj);

    });
}