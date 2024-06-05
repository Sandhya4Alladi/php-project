const inputUsername = document.getElementById('inputUsername');
const inputEmail = document.getElementById('inputEmail');
const editButton = document.getElementById('editButton');
const deleteButton = document.getElementById('deleteButton');
    
let isEditMode = false;
    
function toggleEditMode() {
    if(!isEditMode){
        document.getElementById("form-div").classList.remove("bg-white")
        document.getElementById("form-div").classList.add("bg-secondary")
    }else{
        document.getElementById("form-div").classList.add("bg-white")
        document.getElementById("form-div").classList.remove("bg-secondary")
    }
    isEditMode = !isEditMode;
    inputUsername.disabled = !isEditMode;
    inputEmail.disabled = !isEditMode;
    editButton.textContent = isEditMode ? 'Save' : 'Edit';
}
    
editButton.addEventListener('click', function() {
    if (!isEditMode) {
        toggleEditMode();
    } else {
        const requestBody = {
            username: inputUsername.value,
            email: inputEmail.value
        };
        console.log(requestBody)
        
        fetch("/user/editprofile?userId="+userId._id['$oid'], {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestBody)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to update user');
            }
            console.log('User updated successfully');
            toggleEditMode();
        })
        .catch(error => {
            console.error('Error updating user:', error);
        });
    }
});
 
 
deleteButton.addEventListener('click', function(event) {
    event.preventDefault();
    $('#deleteConfirmationModal').modal('show');
});
  
document.getElementById('confirmDeleteButton').addEventListener('click', function() {
    console.log('Confirmed action');
    fetch("/user/deleteprofile?userId="+userId._id['$oid'], {
        method: 'DELETE'
    }).then(res => {
        if (res.status === 200) {
            console.log("hi");
            fetch('/auth/logout',{
                method: 'GET'
            }).then(response => {
                window.location.href = "/auth/signup";
            });
        } else {
            console.log(error);
        }
    }).catch(error => {
        console.error('Fetch error:', error);
    });
    $('#confirmationModal').modal('hide');
});  
