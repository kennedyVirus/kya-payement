window.onload = function () {
    var app = new Vue({
        delimiters: ['${', '}'],
        el: '#app',
        data: {
            new_enterprise:{
                email:'',
                phone_number:'',
                first_name:'',
                last_name:'',
                address:'',
                country:'',
                city:'',
                job_title:'',
                organisation:'',
                amount_category:'',
                type:1
            },
            new_academic:{
                email:'',
                phone_number:'',
                transaction_phone_number:'',
                first_name:'',
                last_name:'',
                address:'',
                country:'',
                city:'',
                job_title:'',
                organisation:'',
                amount_category:'',
                type:2
            },
            new_student:{
                email:'',
                phone_number:'',
                transaction_phone_number:'',
                first_name:'',
                last_name:'',
                address:'',
                country:'',
                city:'',
                job_title:'',
                organisation:'',
                amount_category:'',
                type:3
            },
            recover_email:'',
            recover_transaction_ref:'',


        },

        mounted: function () {

            $('.modal').modal();

            $('#modal-loader').modal({
                    dismissible: false, // Modal can be dismissed by clicking outside of the modal
                    opacity: 0.5, // Opacity of modal background
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    startingTop: '4%', // Starting top style attribute
                    endingTop: '10%', // Ending top style attribute
                }
            );

           // $('#modal-loader').modal('open');


            $('.collapsible').collapsible();

        },


        methods : {

            openPay1Modal(){
                $('#modal_pay_enterprise').modal('open')
            },
            openRecoverModal(){
                $('#modal_recover_key').modal('open')
            },
            recoverKey(){
                if(this.recover_email!='' || this.recover_transaction_ref!=''){

                    let data={
                        'email':this.recover_email,
                        'transaction_ref':this.transaction_ref,
                    }
                    $('#modal-loader').modal('open');

                    Swal.fire({
                        title: 'Desolé!',
                        text: "Aucun résultat ne correspond à votre recherche",
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                    // Swal.fire({
                    //     title: 'Error!',
                    //     text: 'Problème de connexion , actualiser la page svp',
                    //     icon: 'error',
                    //     confirmButtonText: 'OK'
                    // })

                    // Swal.fire({
                    //     title: 'Opération réussie',
                    //     text: message,
                    //     icon: 'success',
                    //     confirmButtonText: 'OK'
                    // });

                    // axios.post('/fflll',data)
                    //     .then((response)=>{
                    //         $('#modal-loader').modal('close');
                    //
                    //        // if(response.data.error===0){
                    //             Swal.fire({
                    //                 title: 'Desolé!',
                    //                 text: "Aucun résultat ne correspond à votre recherche",
                    //                 icon: 'warning',
                    //                 confirmButtonText: 'OK'
                    //             })
                    //       //  }
                    //
                    //     })




                }

            },

            studentStep1(){
                $('#modal_pay_student1').modal('open')
            },
            studentStep2(){
                $('#modal_pay_student1').modal('close')
                $('#modal_pay_student2').modal('open')
            },
            enterpriseStep1(){
                $('#modal_pay_enterprise1').modal('open')
            },
            enterpriseStep2(){
                $('#modal_pay_enterprise1').modal('close')
                $('#modal_pay_enterprise2').modal('open')
            },
            payPaygate(){
                var checked = false

                let selected = '';

                for (let i = 1;  i < 7 ; i++) {

                    if($('#student'+i).is(':checked')) {
                        checked = true;
                        selected = i;
                        break;
                    }
                }
                if (checked === true) {
                    this.new_student.amount_category=selected

                    $('#modal-loader').modal('open');
                    console.log(this.new_student)
                axios.post('/8004064b17546e4380ce83d1be75b50dkfj2015/api/kya/paygate/payment/init',this.new_student)
                    .then((response)=>{
                        $('#modal-loader').modal('close');

                        $('#modal_pay_student2').modal('close')

                        console.log(response.data)
                        if(response.data.error===0){
                            if(response.data.data.type===1){
                                Swal.fire({
                                    title: 'Confirmation!',
                                    text: "Vous serez redirigé vers un site marchand pour continuer l'opération",
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.value) {
                                        window.location.href = response.data.data.url
                                    }
                            })}
                            else {
                                Swal.fire({
                                    title: 'Confirmation!',
                                    text: "Veuillez consulter votre messagerie pour continuer l'opération",
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                })
                            }
                        }else{
                            Swal.fire({
                                title: 'Error!',
                                text: 'Oups.Une erreur est survenue , réssayez svp',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            })
                        }
                    }).catch((error)=>{
                        console.log(error)
                })
            }
            },

            payPaydunya(){
                var checked = false

                let selected = '';

                for (let i = 4;  i < 7 ; i++) {

                    if($('#enterprise'+i).is(':checked')) {
                        checked = true;
                        selected = i;
                        break;
                    }
                }
                if (checked === true) {
                    this.new_enterprise.amount_category=selected

                    $('#modal-loader').modal('open');
                    console.log(this.new_enterprise)
                axios.post('/8004064b17546e4380ce83d1be75b50dkfj2015/api/kya/paydunya/payment/init',this.new_enterprise)
                    .then((response)=>{
                        $('#modal-loader').modal('close');

                        $('#modal_pay_enterprise2').modal('close')

                        console.log(response.data)

                        // console.log(response.data)
                        if(response.data.error===0){
                                Swal.fire({
                                    title: 'Confirmation!',
                                    text: "Vous serez redirigé vers un site marchand pour continuer l'opération",
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.value) {
                                        window.location.href = response.data.data.url
                                    }
                            })

                        }else{
                            Swal.fire({
                                title: 'Error!',
                                text: 'Oups.Une erreur est survenue , réssayez svp',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            })
                        }
                    })
                //     .catch((error)=>{
                //         console.log(error)
                // })
            }
            }
        },
        filters:{

        }


    })
}