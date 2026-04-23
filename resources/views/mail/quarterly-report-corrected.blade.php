<x-mail::message>
# Your Quarterly Report Has Been Updated

Hello {{ $customerName }},

Good news! The corrections you requested for your quarterly report have been completed.

<x-mail::panel>
**Report Period:** {{ $period }}

**Status:** Pending Your Review

The report has been updated based on your feedback and is now ready for your review.
</x-mail::panel>

## Next Steps:

1. Review the updated report
2. If everything looks good, approve the report
3. Once approved, you can sign the report

<x-mail::button :url="$viewUrl">
Review Updated Report
</x-mail::button>

If you have any additional questions or concerns about the report, please don't hesitate to add notes or request further corrections.

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
