function sort_date_desc(a, b) {
    return new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime();
}

function renderMessages(msgArray)
{
    $("#message_list").html("");
    let msg_list_array = [];

    let group_msg = {};
    let received_id = null;
    $.each(msgArray, function(i, item){
        let data = groupArray(item.sender_id, item.receiver_id, msgArray);
        const group_name = item.sender_id + "_" + item.receiver_id;
        const reverse_group_name = item.receiver_id + "_" + item.sender_id;
        
        if(!group_msg[reverse_group_name])
        {
            group_msg[group_name] = data;
        }
    });

    if(profile.getReceiver()==null)
    {
        $("#send_massage").hide();
        console.log(group_msg);
        $.each(group_msg, function(i, item){
            let obj = item[item.length - 1];
            if(obj.sender_id == profile.getID())
            {
                received_id = obj.receiver_id;
            }else{
                received_id = obj.sender_id;
            }
            let new_obj = {"sender_name":obj.sender_name,"msg":obj.msg,"timestamp":obj.timestamp,"received_id":received_id};
            msg_list_array.push(new_obj);
        });

        msg_list_array.sort(sort_date_desc);
        for(i in msg_list_array)
        {
            $("#message_list").append(generateMsgBoxListView(msg_list_array[i].sender_name, msg_list_array[i].msg, msg_list_array[i].timestamp, msg_list_array[i].received_id));
        }

    }else{
        $("#send_massage").show();
        $.each(group_msg, function(i, item){
            for(i in item){
                if(item[i].receiver_id==profile.getReceiver() && item[i].sender_id==profile.getID() || item[i].receiver_id==profile.getID() && item[i].sender_id==profile.getReceiver()){
                    $("#message_list").append(generateMsgBox(item[i].sender_name, item[i].msg, item[i].timestamp, item[i].sender_id));
                }
            }
        });
    }
}

function generateMsgBoxListView(sender_name, msg, timestamp, receiver_id)
{
    let html = "<div class='last_msg_box' data-receiver='" + receiver_id + "'>";
    html += "<div class='header_box'>" + sender_name + " - " + timestamp + "</div>";
    html += "<span class='last_msg_box_txt'>" + msg + "</span>";
    html += "</div>";
    
    return html;
}

function generateMsgBox(sender_name, txt, timestamp, sender_id)
{
    let position = "box_right";
    let bottomClass = "tab_right";

    if(sender_id == profile.getID())
    {
        position = "box_left";
        bottomClass = "tab_left";
    }

    const clName = 'msg_box ' + position;
    let html = "<div class='" + clName + "'>";
    html += "<span class='msg_box_from'>" + sender_name + "</span>";
    html += "<span class='msg_box_txt'>" + txt + "</span>";
    html += "</div>";
    html += "<div class='msg_box_bottom " + bottomClass + "'> <span class='msg_box_date'>" + timestamp + "</span> </div>";

    return html;
}

function groupArray(s,r,array)
{

    let collection = [];
    for(let i in array) {
        if(array[i]['sender_id'] == s && array[i]['receiver_id'] == r)
        {
            collection.push(array[i]);
        }
        if(array[i]['sender_id'] == r && array[i]['receiver_id'] == s)
        {
            collection.push(array[i]);
        }
    }	
    return collection;
}
