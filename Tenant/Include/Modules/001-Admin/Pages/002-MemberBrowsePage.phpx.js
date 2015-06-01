// these scripts go on ALL WebPages defined in MemberBrowsePage.phpx
// if you want a script on a particular page, define a <Script> in the
//		<Page> / <Scripts> tag.

window.addEventListener("load", function(e)
{
	var cmdMemberPropertiesDiscardChanges = document.getElementById("cmdMemberPropertiesDiscardChanges");
	cmdMemberPropertiesDiscardChanges.addEventListener("click", function(e)
	{
		wndMemberProperties.Hide();
		
		e.preventDefault();
		e.stopPropagation();
		return false;
	});
});

function lvMembers_ItemActivate()
{
	var value = document.getElementById('lvMembers').NativeObject.GetSelectedRows()[0].get_Value();
	System.Redirect("~/members/" + value + "/modify");
}