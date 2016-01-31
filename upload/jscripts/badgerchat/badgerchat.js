//TODO: Put into class
function badgerchat_loadMessages(successCallback, errorCallback){
    $.get(badgerchat_data.loadUrl)
        .done(function(messages) {
            // TODO: try catch
            var deserialisedMessages = JSON.parse(messages);
            successCallback(deserialisedMessages);
        })
        .fail(function() {
            errorCallback();
        });
}

var badgerchat_addMessage_responses = {
    Success: 1,
    Unauthorised: 2
};

function badgerchat_addMessage(message, successCallback, errorCallback){
    $.post(badgerchat_data.addMessageUrl, { "badgerchat_message" : message })
        .done(function(result){
            var resultObject = JSON.parse(result);
            if(resultObject.Status == badgerchat_addMessage_responses.Success){
                successCallback(resultObject.Message);
            }
            else {
                errorCallback(resultObject.Status);
            }
        })
        .fail(function(){
            errorCallback(-1);
        });
}
