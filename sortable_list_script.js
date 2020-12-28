$(function () {
    const $sortable = $("#sortable");
    $sortable.sortable();
    $sortable.disableSelection();
});

document.getElementById("vote").addEventListener("click", function () {
    let serializedIDs = $("#sortable").sortable('serialize');
    function getElectionId() {
        return document.getElementById('election_id').innerText;
    }

    serializedIDs = "election_id[]="+getElectionId()+"&"+serializedIDs;

    window.location.href = "register_vote.php"+"?"+serializedIDs;
})