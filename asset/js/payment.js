
function SparkleCheckout(payment) {
    var url = "http://localhost:8888/sparkle/api/js_transfer";
    const data = {
        "tx_ref": payment.tx_ref,
        "amount": payment.amount,
        "currency": payment.currency,
        "callback_url": payment.callback_url,
        "return_url": payment.return_url,
        "customer": {
            "email": payment.customer.email,
            
            "first_name": payment.customer.first_name,
            "last_name": payment.customer.last_name,
        },
        "customization": {
            "title": payment.customization.title,
            "description": payment.customization.description,
            "logo": payment.customization.logo
        },
        "meta": payment.meta
    };
    fetch(url, {
        method: "POST",
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' , 'Authorization': ' Bearer '+payment.secret_key },
        body: JSON.stringify(data)
    }).then(response => response.json())
        .then(data => {
            if(data.data!=null){
                //document.getElementById("message").innerHTML = data.data.checkout_url;
                window.location.href=data.data.checkout_url;
            }else{
                document.getElementById("message").innerHTML = data.message;
            }
            console.log('Success:', data);
        })
        .catch((error) => {
            console.error('Error:', error);
        });

}