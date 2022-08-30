



const sitBody = document.querySelector('body');


function hideSidePanel(){
    const sidePanel = document.querySelector('.side-panel');
    console.log(sidePanel.className);
}

sitBody.addEventListener("click", function(event){
    if(!event.target.closest('.side-panel') && !event.target.closest('.side-panel-btn')){
        hideSidePanel();
    }
    console.log(event.target.className);
});