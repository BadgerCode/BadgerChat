$(document).ready(function(){
    var inputBox = $("#badgerchatMessageInput");
    var addMessageButton = $("#badgerchatMessageSubmit");
    var chatBoxBody = $(".badgerchat-index-chatbox-body");

    function LoadMessages(){
        badgerchat_loadMessages(
            function(messages){
                ClearMessages();
                RenderMessages(messages);
                ScrollToBottomOfMessages();
            }, function(){
                console.log("Error loading messages");
            }
        );
    }

    function ClearMessages(){
        chatBoxBody.html("");
    }

    function AddMessage(message){
        chatBoxBody.append("<div class=\"badgerchat-index-chatbox-row\">" + message.User
                            + ": <div class=\"badgerchat-index-chatbox-row-message\">" + message.Message + "</div></div>");
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
                var isAtBottom = IsScrolledToBottom();
                AddMessage(addedMessage);

                if(isAtBottom){
                    ScrollToBottomOfMessages();
                }
            }, function(reason){
                console.log("Failed: " + reason);
                inputBox.attr("disabled", false);
            }
        );
    }

    function IsScrolledToBottom(){
        /*
         http://stackoverflow.com/questions/18614301/keep-overflow-div-scrolled-to-bottom-unless-user-scrolls-up
         dotnetCarpenter
         */
        var messageContainerElement = chatBoxBody[0];
        var chromeScrollInaccuracy = 1;
        return messageContainerElement.scrollHeight - messageContainerElement.clientHeight
                <= messageContainerElement.scrollTop + chromeScrollInaccuracy;
    }

    function ScrollToBottomOfMessages(){
        /*
         http://stackoverflow.com/questions/18614301/keep-overflow-div-scrolled-to-bottom-unless-user-scrolls-up
         dotnetCarpenter
         */
        var messageContainerElement = chatBoxBody[0];
        messageContainerElement.scrollTop = messageContainerElement.scrollHeight - messageContainerElement.clientHeight;
    }

    // TODO: Check if enter is pressed in input and submit message

    addMessageButton.click(function(event){
        event.preventDefault();
        SubmitMessage();
    });

    LoadMessages();
});