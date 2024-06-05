document.getElementById('confirmButton').addEventListener('click', function(event) {
    event.preventDefault(); 
    $('#confirmationModal').modal('show'); 
});
  
document.getElementById('confirmActionButton').addEventListener('click', function() {
    console.log('Confirmed action');
    fetch('/auth/logout',{
        method: 'get'
    })
    .then(response => {
        console.log(response.status);
        if(response.status === 200){
            window.location.href = '/auth/login'
        }else{
            window.location.href = '/video/home'
        }
    })
    $('#confirmationModal').modal('hide');
});  