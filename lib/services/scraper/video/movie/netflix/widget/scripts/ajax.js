/*
 * empty
 * PHP-like check for null/empty values
 *   http://kevin.vanzonneveld.net
 * +   original by: Philippe Baumann
 * +      input by: Onno Marsman
 * +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
 * +      input by: LH
 * +   improved by: Onno Marsman
 * +   improved by: Francesco
 * +   improved by: Marc Jansen
 * +   input by: Stoyan Kyosev (http://www.svest.org/)
 *     example 1: empty(null);
 *     returns 1: true
 *     example 2: empty(undefined);
 *     returns 2: true
 *     example 3: empty([]);
 *     returns 3: true
 *     example 4: empty({});
 *     returns 4: true
 *     example 5: empty({'aFunc' : function () { alert('humpty'); } });
 *     returns 5: false
 */
function empty(mixed_var) {
    var key;

    if (mixed_var === "" || mixed_var === 0 || mixed_var === "0" || mixed_var === null || mixed_var === false || typeof mixed_var === 'undefined') {
        return true;
    }

    if (typeof mixed_var == 'object') {
        for (key in mixed_var) {
            return false;
        }
        return true;
    }

    return false;
}

/*
 * GUP
 *   gup stands for 'Get URL Paramaters' (values retrieved by name)
 *
 * Usage:
 *   Your URL looks like  "http://site.com/service?user=frank
 *   var user_param = gup('user');  
 *  "user_param" would be set to String "frank"
 *
 *@param name String the name of the parameter to get the value of
 *@return results String representation of the text in the URL parameter
 *@author Justin Barlow	
 *@source http://www.netlobo.com/url_query_string_javascript.html
 * From the author:
 *     "Most of the server-side programming languages that I know of like PHP, ASP, or JSP give you easy access to parameters in the query string of a URL. Javascript does not give you easy access. With javascript you must write your own function to parse the window.location.href value to get the query string parameters you want. Here is a small function I wrote that will parse the window.location.href value and return the value for the parameter you specify. It does this using javascript's built in regular expressions"
 */
function gup(name, _url)
{
  var theURL = (!empty(_url)) ? _url : window.location.href;
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(theURL);
  if(results == null) {
    return "";
  }
  else {
    return results[1];
  }  
}