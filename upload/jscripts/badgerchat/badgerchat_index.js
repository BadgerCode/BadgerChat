$(document).ready(function(){
    var inputBox = $("#badgerchatMessageInput");
    var addMessageButton = $("#badgerchatMessageSubmit");
    var chatBoxBody = $(".badgerchat-index-chatbox-body");

    function LoadMessages(){
        badgerchat_loadMessages(
            function(messages){
                ClearMessages();
                RenderMessages(messages);
            }, function(){
                console.log("Error loading messages");
            }
        );
    }

    function ClearMessages(){
        chatBoxBody.html("");
    }

    function AddMessage(message){
        chatBoxBody.append("<div class=\"badgerchat-index-chatbox-row\">" + message.User + ": " + message.Message + "</div>");
    }

    function RenderMessages(messages){
        $.each(messages, function(index, message){
            AddMessage(message);
        });
    }

    function SubmitMessage(){
        var message = inputBox.val();
        inputBox.attr("disabled", true);

        badgerchat_addMessage(message,
            function(addedMessage){
                inputBox.val("");
                inputBox.attr("disabled", false);

                // TODO: Use returned message object
                AddMessage({User: "You", Message: addedMessage});
            }, function(reason){
                console.log(reason);
                inputBox.attr("disabled", false);
            }
        );
    }

    // TODO: Check if enter is pressed in input and submit message

    addMessageButton.click(function(event){
        event.preventDefault();
        SubmitMessage();
    });

    LoadMessages();
});