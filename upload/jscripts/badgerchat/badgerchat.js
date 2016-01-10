$(document).ready(function(){

    var chatBoxBody = $(".badgerchat-index-chatbox-body");
    badgerchat_loadMessages(chatBoxBody)
});

function badgerchat_loadMessages(chatBoxBody){
    var messages = badgerchat_getMessages();
    badgerchat_clearMessages(chatBoxBody);
    badgerchat_renderMessages(chatBoxBody, messages);
}

function badgerchat_getMessages(){
    return [
        { User: "Badger", Message: "Hello world!"},
        { User: "Badger", Message: "etc!"},
        { User: "Badger", Message: "bye!"},
    ];
}

function badgerchat_clearMessages(chatBoxBody){
    chatBoxBody.html("");
}

function badgerchat_renderMessages(chatBoxBody, messages){
    $.each(messages, function(index, message){
        chatBoxBody.append("<div class=\"badgerchat-index-chatbox-row\">" + message.User + ": " + message.Message + "</div>");
    });
}