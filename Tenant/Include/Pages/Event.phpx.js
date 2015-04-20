function HasUserSelectedMealPlan()
{
	return (wizard_step2_optMealPlan1.checked || wizard_step2_optMealPlan2.checked || wizard_step2_optMealPlan3.checked || wizard_step2_optMealPlan4.checked);
}

window.addEventListener("load", function()
{
	// bind the events
	var cmdAddExtraGuest = document.getElementById("cmdAddExtraGuest");
	if (cmdAddExtraGuest != null)
	{
		cmdAddExtraGuest.addEventListener("click", function(e)
		{
			wndGuestDetails.ShowDialog();
			
			e.preventDefault();
			e.stopPropagation();
			return false;
		});
	}
	
	var cmdGuestDetailsCancel = document.getElementById("cmdGuestDetailsCancel");
	var cmdGuestDetailsSaveChanges = document.getElementById("cmdGuestDetailsSaveChanges");
	
	if (cmdGuestDetailsCancel != null)
	{
		cmdGuestDetailsCancel.addEventListener("click", function(e)
		{
			wndGuestDetails.Hide();
			
			e.preventDefault();
			e.stopPropagation();
			return false;
		});
	}
	if (cmdGuestDetailsSaveChanges != null)
	{
		cmdGuestDetailsSaveChanges.addEventListener("click", function(e)
		{
			if (document.getElementById("cboMealPlan").NativeObject.GetSelectedItems().length == 0)
			{
				alert("Please choose a meal plan");
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
			
			wndGuestDetails.SetLoading(true);
			wndGuestDetails.SetLoadingText("Saving your changes, please wait");
			
			alert("You chose " + document.getElementById("cboMealPlan").NativeObject.GetSelectedItems()[0].Title + "!");
			
			e.preventDefault();
			e.stopPropagation();
			return false;
		});
	}
	
	var wizard_step2_optMealPlan1 = document.getElementById("wizard_step2_optMealPlan1");
	var wizard_step2_optMealPlan2 = document.getElementById("wizard_step2_optMealPlan2");
	var wizard_step2_optMealPlan3 = document.getElementById("wizard_step2_optMealPlan3");
	var wizard_step2_optMealPlan4 = document.getElementById("wizard_step2_optMealPlan4");
	
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
	
	var chkSpecialDietaryNeeds = document.getElementById("chkSpecialDietaryNeeds");
	
	var divSpecialDietaryNeeds = document.getElementById("divSpecialDietaryNeeds");
	divSpecialDietaryNeeds.style.display = "none";
	
	chkSpecialDietaryNeeds.addEventListener("change", function(e)
	{
		if (this.checked)
		{
			divSpecialDietaryNeeds.style.display = "block";
		}
		else
		{
			divSpecialDietaryNeeds.style.display = "none";
		}
	});
});