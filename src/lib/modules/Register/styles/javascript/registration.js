$(document).ready(function () {
    $("#datepicker").datepicker();
    $("form.registration-form").bootstrapValidator({
        feedbackIcons: {
            valid: 'fa fa-check',
            invalid: 'fa fa-times',
            validating: 'fa fa-refresh'
        },
        message: "This field cannot be blank!",
        submitButtons: "button[type='submit'],input[type='submit']",
        fields: {
            username: {
                validators: {
                    stringLength: {
                        min: 3,
                        message: "Your username is too short (min. 3 chars)"
                    }
                }
            },
            firstname: {
                validators: {
                    stringLength: {
                        min: 3,
                        message: "Your firstname is too short (min. 3 chars)"
                    }
                }
            },
            lastname: {
                validators: {
                    stringLength: {
                        min: 3,
                        message: "Your lastname is too short (min. 3 chars)"
                    }
                }
            },
            email: {
                validators: {
                    emailAddress: {
                        message: "This is not a valid email-address"
                    },
                    callback: {
                        callback: function (field, validator) {
                            var t = sendRequest({val: field, field: "email"}, "register", "validate", function (r) {
                                validateMail(r.valid, validator);
                            });
                        },
                        message: "Email-Address already in use"
                    }
                }
            },
            password: {
                validators: {
                    identical: {
                        field: 'password_repeat',
                        message: 'The password and its confirm are not the same'
                    },
                    stringLength: {
                        min: 6,
                        message: "The password is too short (min. 6 chars)"
                    }
                }
            },
            password_repeat: {
                validators: {
                    identical: {
                        field: 'password',
                        message: 'The password and its confirm are not the same'
                    },
                    stringLength: {
                        min: 6,
                        message: "The password is too short (min. 6 chars)"
                    }
                }
            },
            birthday: {
                validators: {
                    date: {
                        format: 'MM/DD/YYYY',
                        message: 'That is not a valid date'
                    }
                }
            }
        }
    });
    $('#datepicker').on('changeDate', function () {
        $('#datepicker').datepicker('hide');
        $('form.registration-form').data('bootstrapValidator').updateStatus('birthday', 'NOT_VALIDATED', null).validateField('birthday');
    }).on('show', function () {
        $('form.registration-form').data('bootstrapValidator').updateStatus('birthday', 'VALIDATING', null);
    });
});

function validateMail(r, v) {
    v.updateStatus("email", (r) ? "VALID" : "INVALID", "callback");
}