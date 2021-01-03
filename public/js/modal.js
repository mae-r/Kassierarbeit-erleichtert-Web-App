// Diese File beinhaltet Funktionen um das Modal zu erzeugen


/* Beispiel: 
   let modal_args = {
          title: "Zahlung",
          header_btn: [
            {type: "undo", confirm: {action: "reset", text: "Zahlung als unbezahlt speichern?"}} , {type: "delete", confirm: {action: "delete", text:"Zahlungsauftrag komplett löschen?"}}
          ],
          form_action: "zahlung_handler.php",
          form_elements: [
              {type: "text", label: "Name:", attr: {value: name, readOnly: true}, group_addon{text:"CHF"}},
              {type: "dropdown", label: "Zahlungs-mittel:", attr: {name: "zahlungsmittel", value: zahlungsmittel, disabled: true}, options: [{text: "Bar"},{text: "TWINT"}], edit_button: true},
              {type: "date", label: "Datum:", date: datum, attr: {name:"date", readOnly: true}, edit_button: true},
              {type: "text", hidden: true, attr: {value: za_id,name: "za_id"}},              
          ],
          btn:{text: "Änderungen speichern"}
          classNames:{form_row:"form-row my-3"}

        };
    
    //new modal:
    modal = new modaljs;
    
    //Aussehen des Modals definieren
    modal.initialize(modal_args)
    
    //Modal anzeigen
    modal.show()

*/


//Diese Funktion wird oft benutzt und kreiert ein Element mit einer Klasse
function create_element (type,className=""){
    let element = document.createElement(type);
    element.className = className;
    return element;
}


!function(window){

    function create_button(icon,style){
        let i_icon = create_element("i",icon);
        let button = create_element("button","btn btn-"+style);
        button.type="button";
        //füge Icon auf den Button hinzu
        button.appendChild(i_icon);
        return button;
    }

{// Funktionen die das Form des Modals betreffen
    
    function create_form(elements,classNames="") {

        let div = create_element("div","modal-body");
        
        for (let element in elements) {
            div.appendChild(create_row(elements[element],classNames));
        }
        return div;
    }
    function create_row(specs,classNames) {
        
        let label = create_label(specs.label);
        let input = form_input(specs);

        if (specs.hidden == true) {
            input.style.display = "none";
            return input;
        }
        let row_elements=[label, input];
        if (specs.edit_button == true){
            row_elements.push(edit_button(input));
        }
        if (specs.delete_button == true){
            row_elements.push(delete_button(input))
        }
        return form_row(row_elements,classNames.form_row);
    }
    
    
    function form_row(elements,className="form-row my-4") {
        let form_row = create_element("div",className);
        for (let element of elements){
            form_row.appendChild(element);
        }

        return form_row;
    }

    function form_input(specs) {
        let input_div = create_element("div","col");
        
        let input;
        //für jede Input-Art wird eine andere Funktion ausgeführt
        if (specs.type == "text") {
            input = inputB_text();
        }
        else if (specs.type == "date") {
            input = inputB_date(specs.date);
        }
        else if (specs.type == "dropdown") {
            // Wenn das Menü deaktiviert ist, muss trotzdem der Wert noch gesendet werden.
            let dropdown_value = form_input({type:"text", attr: {value: specs.attr.value, name: specs.attr.name, style:"display: none"}})
            input_div.appendChild(dropdown_value);
            input = inputB_dropdown(specs.options);
        }
        else if (specs.type == "checkbox"){
            input = input_checkbox(specs);
            input_div.appendChild(input);
            return input_div;
        }
        else {
            return input_div;
        }
        //jedes Attribut wird dem Element beigefügt
        for (let attr in specs.attr) {
            input[attr] = specs.attr[attr];
        }
        input.className += " form-control";
   
        input_div.appendChild(input);

        //Text hinter dem Input Feld
        if(specs.group_addon){
            let div = create_element("div","input-group-append");
            let group_addon = create_element("span","input-group-text");
            group_addon.innerHTML=specs.group_addon.text;
            div.appendChild(group_addon);
            input_div.appendChild(div);
            input_div.className +=" input-group";
        }
        
        return input_div;
    }

    

{    //Funktionen für die verschiedenen Form-Elemente

    function create_label(text){
        if (text === undefined){
            return create_element("div");
        }
        let label = create_element("label");
        label.innerHTML = text;

        let label_div = create_element("div","col-3 text-center");
        label_div.appendChild(label);
        return label_div;
    }
    
    function inputB_text() {
        let input = create_element("input");
        input.type = "text";
        return input;
    }

    function inputB_dropdown(options) {
        let input = create_element("select","");
        for (let id in options) {
            var opt = create_element("option","");
            for (let attr in options[id]) {
                opt[attr] = options[id][attr];
            }
            input.appendChild(opt);
        }
        return input;
    }

    function inputB_date(default_date) {
        //Als Grundlage wird ein Text-Input gewählt
        let input = inputB_text();
        input.setAttribute("data-provide", "datepicker");

        //für die Konvertierung des Datums wird dieser Code durchgeführt (sehr unschön)
        let datepicker_temp = create_element("input");
        document.getElementsByTagName("body")[0].appendChild(datepicker_temp);
        datepicker_temp.setAttribute("data-provide", "datepicker");
        $(datepicker_temp).datepicker('setDate', default_date);
        let date = datepicker_temp.value;
        datepicker_temp.parentNode.removeChild(datepicker_temp);
        
        input.value = date;
        return input;
    }

    //in dieser Variable werden alle ids gespeichert, damit es keine doppelten Einträge gibt. (unschöne Methode)
    let unique_ids = [""];
    
    function input_checkbox(specs){
        let div = create_element("div","custom-control custom-checkbox");
        let input = create_element("input");
        input.type = "checkbox";
        
        for (let attr in specs.check) {
            input[attr] = specs.check[attr];
        }
        input.className += " custom-control-input";

        //eine id für die checkbox muss zufällig erzeugt werden
        let id = input.id;
        while (unique_ids.includes(id)){
            id = "checkbox_modal" + Math.floor(Math.random() * 100000).toString();
        }
        unique_ids.push(id);
        input.id = id;

        let label = create_element("label");
        for (let attr in specs.check_label) {
            label[attr] = specs.check_label[attr];
        }
        label.className += " custom-control-label";
        if (specs.check_label.readOnly !== undefined){
            label.setAttribute("readonly", "true");
        }
        label.htmlFor = id;
        
        
        div.appendChild(input);
        div.appendChild(label);
        return div;
    }

    //Funktion für Button mit Pencil-Icon, ! Icons müssen in die Website eingebunden werden !
    function edit_button(element){
        function edit_click(edit_btn,element){
            edit_btn.children[0].style.display="none";
            element.children[0].readOnly=false;
            element.children[0].disabled=false;
            if (element.children.length > 1 && element.children[1].nodeName =="SELECT"){
                element.children[1].disabled = false;
            }
        }
        
        let div = create_element("div","");

        let button = create_button("fas fa-pencil-alt","light");
        button.addEventListener('click', () => {edit_click(div,element)})

        div.appendChild(button);
        return div;
    }

    //Funktion für Button mit Delete-Icon, 
    function delete_button(element){
        let div = create_element("div","");

        let button = create_button("fas fa-trash-alt","light");
        button.addEventListener('click', () => {
            element.children[0].value=false;
            element.children[0].disabled=false;
            element.parentNode.style="display: none";
        })

        div.appendChild(button);
        return div;
    }

}

}
    function confirm_row(form){
        //Eine Zeile mit der Bestätigungsaufforderung wird angezeigt
        
        let div_row = create_element("div","row ml-3 mr-3");
        div_row.style="display: none"
        let div_col1 = create_element("div","col");
        //Erzeugt ein verstecktes Feld in dem der Befehl gespeichert wird
        let action = form_input({type:"text", attr: {name: "action", style:"display: none"}});
        div_col1.appendChild(action)
        let label = create_element("label");
        div_col1.appendChild(label);

        let div_col2 = create_element("div","text-right");

        let no = create_button("fas fa-times","danger mr-1"); // mr-1: Leerraum zwischen no und yes button
        no.className += "";
        //Wenn no gedrückt wird, wird ganze Zeile versteckt
        no.addEventListener('click', () => {div_row.style="display: none"})
        div_col2.appendChild(no);

        div_row.appendChild(div_col1);
        div_row.appendChild(div_col2);

        let yes = create_button("fas fa-check","success");
        div_col2.appendChild(yes);

        //wird diese Funktion aufgerufen, wird die bestätigungsmeldugn angezeigt 
        //action value ist der Wert der bei eingültiger Bestätgung im Feld "action" gesendet wird
        let open_confirm = (text,action_value) => {
            //der vorherige yes button wird entfernt
            yes.parentNode.removeChild(yes);
            //neuer yes button wird hinzugfügt ohne schon vorhandene event listener
            yes = create_button("fas fa-check","success");
            div_col2.appendChild(yes);
            yes.addEventListener('click', () => {
                action.childNodes[0].value = action_value;
                form.submit();
            } )
            label.innerHTML = text;
            div_row.style = "";
        }
        return [div_row,open_confirm];

    }
    
{//Funktionen welche einzelne Bestandteile des Modals erzeugen
    function modal_header(title){
        let title_heading = create_element("h5","modal-title");
        title_heading.innerHTML=title;
        
        let div = create_element("div","modal-header");
        div.appendChild(title_heading);
        return div;

    }

    function modal_footer(btn){
        let button = create_element("button","btn btn-primary")
        button.type="submit";
        button.textContent=btn.text;
        let div = create_element("div","modal-footer");
        div.appendChild(button);
        return div;
    }

    function modal_main(){
        let modal = create_element("div","modal fade");
        let m_dialog = create_element("div","modal-dialog");
        modal.appendChild(m_dialog);
        let m_content = create_element("div","modal-content");
        m_dialog.appendChild(m_content);
        return [modal, m_content];
    }
    function modal_main_form(action){
        let form = create_element("form");
        form.action = action;
        form.method = "POST";
        
        return form;
    }
}    
    //alle Button im Header werden erzeugt
    function header_btn(btns,confirm_function){
        let btn_div = create_element("div","ml-auto") //für Buttons

        for (let i in btns) {
            let button;
            if (btns[i].type=="delete"){
                button = create_button("fas fa-trash-alt","light mr-1")
            }
            else if (btns[i].type=="undo"){
                button = create_button("fas fa-undo-alt","light mr-1")
            }
            //für jeden Button wir die confirm_funtion mit den entsprechenden Parameter beim Drücken ausgeführt
            if(btns[i].confirm){
                button.addEventListener('click',() => {confirm_function(btns[i].confirm.text,btns[i].confirm.action)});
            }
            btn_div.appendChild(button);
        }

        //Button um Modal zu schliessen
        let dismiss_btn = create_element("button","close");
        dismiss_btn.type = "button";
        dismiss_btn.setAttribute("data-dismiss", "modal");
        dismiss_btn.innerHTML = "<span>&times;</span>";
        btn_div.appendChild(dismiss_btn);

        return btn_div
    }

    class modaljs{
        constructor(){
            //modal wir dem Body hinzgefügt
            let body = document.getElementsByTagName("body")[0];
            this.main = modal_main()
            body.appendChild(this.main[0]);
        }
        initialize (modal){
            //alle schon im Modal vorhandenen Elemente werden gelöscht
            while (this.main[1].firstChild) {
                this.main[1].removeChild(this.main[1].firstChild);
            }
            //Ein Form welches sich über das ganze Modal streckt
            this.main_form=modal_main_form(modal.form_action);
            this.main[1].appendChild(this.main_form);

            this.header = modal_header(modal.title);
            this.main_form.appendChild(this.header);
            this.form_elements = create_form(modal.form_elements,modal.classNames);
            this.main_form.appendChild(this.form_elements);
            
            //Dies ist das Bestätigungs-Menu
            this.confirm_row = confirm_row(this.main_form);
            this.form_elements.insertBefore(this.confirm_row[0],this.form_elements.children[0]);
            
            this.footer = modal_footer(modal.btn);
            this.main_form.appendChild(this.footer);

            //fügt Buttons hinzu
            this.header.appendChild(header_btn(modal.header_btn,this.confirm_row[1]));

        }
        show(){
            $(this.main[0]).modal("show");
        }
    }
    
    window.modaljs = modaljs;

}(window)