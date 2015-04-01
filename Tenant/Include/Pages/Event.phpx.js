function HasUserSelectedMealPlan()
{
	return (wizard_step2_optMealPlan1.checked || wizard_step2_optMealPlan2.checked || wizard_step2_optMealPlan3.checked);
}

window.addEventListener("load", function()
{
	// bind the events
	var cmdAddExtraGuest = document.getElementById("cmdAddExtraGuest");
	cmdAddExtraGuest.addEventListener("click", function(e)
	{
		wndGuestDetails.ShowDialog();
		
		e.preventDefault();
		e.stopPropagation();
		return false;
	});
	
	var cmdGuestDetailsCancel = document.getElementById("cmdGuestDetailsCancel");
	var cmdGuestDetailsSaveChanges = document.getElementById("cmdGuestDetailsSaveChanges");
	cmdGuestDetailsCancel.addEventListener("click", function(e)
	{
		wndGuestDetails.Hide();
	});
	cmdGuestDetailsSaveChanges.addEventListener("click", function(e)
	{
		wndGuestDetails.SetLoading(true);
		wndGuestDetails.SetLoadingText("Saving your changes, please wait");
	});
	
	var wizard_step2_optMealPlan1 = document.getElementById("wizard_step2_optMealPlan1");
	var wizard_step2_optMealPlan2 = document.getElementById("wizard_step2_optMealPlan2");
	var wizard_step2_optMealPlan3 = document.getElementById("wizard_step2_optMealPlan3");
	
	var wizard_step2_alertMealPlanRequired = document.getElementById("wizard_step2_alertMealPlanRequired");
	
	function optMealPlan_Change(e)
	{
		if (HasUserSelectedMealPlan())
		{
			wizard_step2_alertMealPlanRequired.style.display = "none";
		}
		else
		{
			wizard_step2_alertMealPlanRequired.style.display = "block";
		}
	}
	
	wizard_step2_optMealPlan1.addEventListener("change", optMealPlan_Change);
	wizard_step2_optMealPlan2.addEventListener("change", optMealPlan_Change);
	wizard_step2_optMealPlan3.addEventListener("change", optMealPlan_Change);
	
	var wizard_step2_cmdNext = document.getElementById("wizard_step2_cmdNext");
	wizard_step2_cmdNext.addEventListener("click", function(e)
	{
		if (!HasUserSelectedMealPlan())
		{
			wizard_step2_alertMealPlanRequired.style.display = "block";
			return;
		}
		wizard_step2_alertMealPlanRequired.style.display = "none";
		
		var wizard = document.getElementById('wizard').NativeObject;
		wizard.SetSelectedPageIndex(2);
		
		e.preventDefault();
		e.stopPropagation();
		return false;
	});
});