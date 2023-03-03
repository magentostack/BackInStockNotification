require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/url'

    ],
    function(
        $,
        modal,
        url
    ) {
        var options1 = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            modalClass: 'modal-notify-me notify-me-alert',
        };
        $(document).on('click', '#action-notify-me', function (){
            var CustomerEmail = $(this).attr('data-customer-email');
            var parentProduct = $(this).attr('data-parent-product');
            var childProduct = $(this).attr('data-child-product');
            var websiteId = $(this).attr('data-websiteid');
            var size = $(this).attr('data-product-size');
            if(!CustomerEmail){
                var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    modalClass: 'modal-notify-me',
                    buttons: [{
                        text: $.mage.__('Notify Me'),
                        class: 'mymodal1',
                        click: function () {
                            var modalEmail = $('#email').val();
                            var formKey = $('#form_key').val();
                            if(modalEmail != 0)
                            {
                                if(isValidEmailAddress(modalEmail))
                                {
                                    $.ajax({
                                        url: url.build('notifyme/index/stocknotification'),
                                        type: 'POST',
                                        data: {
                                            email: modalEmail,
                                            parentProductId: parentProduct,
                                            childProductId: childProduct,
                                            websiteId: websiteId,
                                            size: size,
                                            form_key: formKey
                                        },
                                        dataType: 'json',
                                        showLoader: true,
                                        success: function (data, status, xhr){
                                            var responseJson = data.response;
                                            var response = JSON.parse(responseJson);
                                            if (response.message !== undefined) {
                                                var message = response.message;
                                                document.getElementById('response-text').innerHTML = 'Thanks, we will keep you updated!';
                                                var options = {
                                                    type: 'popup',
                                                    responsive: true,
                                                    innerScroll: true,
                                                    modalClass: 'modal-notify-me notify-me-alert',
                                                    buttons: [{
                                                        text: $.mage.__('Continue'),
                                                        class: 'mymodal1',
                                                        click: function () {
                                                            this.closeModal();
                                                        }
                                                    }]
                                                };
                                                var popup = modal(options, $('#response-message'));
                                                $("#response-message").modal("openModal");
                                                $("#response-message").show();
                                            } else {
                                                console.log('Error happens. Try again123.');
                                                $('body').loader('hide');
                                                $("#notify-modal-error").html("Subscription Failed..").modal(options1).modal('openModal');
                                                $(".modal-footer").hide();
                                            }
                                        },
                                        error: function (xhr, status, errorThrown) {
                                            console.log('Error happens. Try again.');
                                            console.log(errorThrown);
                                            $('body').loader('hide');
                                            $("#notify-modal-error").html("Subscription Failed..").modal(options1).modal('openModal');
                                            $(".modal-footer").hide();
                                        }
                                    });
                                    this.closeModal();
                                }
                                else {
                                    document.getElementById('email-error').innerHTML = 'Email is not valid';
                                }
                            }
                            else {
                                document.getElementById('email-error').innerHTML = 'Field is required';
                            }

                        }
                    }]
                };
                var popup = modal(options, $('#popup-modal'));
                $("#popup-modal").modal("openModal");
                $("#popup-modal").show();
            }else {
                var formKey = $('#form_key').val();
                $.ajax({
                    url: url.build('notifyme/index/stocknotification'),
                    data: {
                        email: CustomerEmail,
                        parentProductId: parentProduct,
                        childProductId: childProduct,
                        websiteId: websiteId,
                        size: size,
                        form_key: formKey
                    },
                    type: 'POST',
                    dataType: 'json',
                    showLoader: true,
                    success: function (data, status, xhr){
                        console.log(data.response);
                        var responseJson = data.response;
                        var response = JSON.parse(responseJson);
                        var message = response.message;
                        if(message !== undefined){
                            document.getElementById('response-text').innerHTML = 'Thanks,we will keep you updated!';
                            var options = {
                                type: 'popup',
                                responsive: true,
                                innerScroll: true,
                                modalClass: 'modal-notify-me notify-me-alert',
                                buttons: [{
                                    text: $.mage.__('Continue'),
                                    class: 'mymodal1',
                                    click: function () {
                                        this.closeModal();
                                    }
                                }]
                            };
                            var popup = modal(options, $('#response-message'));
                            $("#response-message").modal("openModal");
                        }else {
                            console.log('Error happens. Try again123.');
                            $('body').loader('hide');
                            $("#notify-modal-error").html("Subscription Failed..").modal(options1).modal('openModal');
                            $(".modal-footer").hide();
                        }

                    },
                    error: function (xhr, status, errorThrown) {
                        console.log('Error happens. Try again.');
                        console.log(errorThrown);
                        $("#notify-modal-error").html("Subscription Failed..").modal(options1).modal('openModal');
                    }
                });
            }

        });

        function isValidEmailAddress(emailAddress) {
            var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
            return pattern.test(emailAddress);
        }

    });
