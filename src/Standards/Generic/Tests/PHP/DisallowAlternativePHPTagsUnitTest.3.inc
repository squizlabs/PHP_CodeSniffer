<!-- This file is essentially the same as the second one, but without the ".fixed" file to allow the warnings to be tested. -->
<div> 
<% echo $var; %>
<p>Some text <% echo $var; %> and some more text</p>
<%= $var . ' and some more text to make sure the snippet works'; %>
<p>Some text <%= $var %> and some more text</p>
</div>
