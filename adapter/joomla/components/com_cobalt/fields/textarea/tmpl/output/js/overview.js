policyViewer.oUI.on(policyViewer.EVENT_TYPE_TEMPLATE_LOADED, function() {
    //var policyData = policyViewer.oData.getOverview();
    var policyData = [
        {
            "name": "URI",
            "title": "URI",
            "description": "Prepare gateway",
            "policies": [
                {
                    "name": "xml2json",
                    "title": "XML => JSON",
                    "description": "Documentation (optional) Transforming user profile  data to JSON for additional formatting before routing.",
                    "category": {
                        "name": "mapping",
                        "title": "Mapping",
                        "description": "",
                        "color": "#13669A"
                    },
                    "configuration": {}
                },
                {
                    "name": "basic_authentication",
                    "title": "Basic Authentication",
                    "description": "Validate user access before proceeding to further policies.",
                    "category": {
                        "name": "authentication",
                        "title": "Authentication",
                        "description": "",
                        "color": "#EDCC82"
                    },
                    "configuration": {}
                },
                {
                    "name": "ldap",
                    "title": "LDAP",
                    "description": "Encrypting this set before sending to destination services.",
                    "category": {
                        "name": "security",
                        "title": "Security",
                        "description": "",
                        "color": "#740C29"
                    },
                    "configuration": {}
                }
            ]
        },
        {
            "name": "SOAP",
            "title": "SOAP",
            "description": "Prepare for target",
            "policies": [
                {
                    "name": "basic_authentication",
                    "title": "Basic Authentication",
                    "description": "Validate user access before proceeding to further policies.",
                    "category": {
                        "name": "authentication",
                        "title": "Authentication",
                        "description": "",
                        "color": "#EDCC82"
                    },
                    "configuration": {}
                },
                {
                    "name": "ldap",
                    "title": "LDAP",
                    "description": "Encrypting this set before sending to destination services.",
                    "category": {
                        "name": "security",
                        "title": "Security",
                        "description": "",
                        "color": "#740C29"
                    },
                    "configuration": {}
                }
            ]
        },
        {
            "name": "SOAP",
            "title": "SOAP",
            "description": "Prepare for Gateway",
            "policies": [
                {
                    "name": "xml2json",
                    "title": "XML => JSON",
                    "description": "Documentation (optional) Transforming user profile  data to JSON for additional formatting before routing.",
                    "category": {
                        "name": "mapping",
                        "title": "Mapping",
                        "description": "",
                        "color": "#13669A"
                    },
                    "configuration": {}
                },
                {
                    "name": "basic_authentication",
                    "title": "Basic Authentication",
                    "description": "Validate user access before proceeding to further policies.",
                    "category": {
                        "name": "authentication",
                        "title": "Authentication",
                        "description": "",
                        "color": "#EDCC82"
                    },
                    "configuration": {}
                },
                {
                    "name": "ldap",
                    "title": "LDAP",
                    "description": "Encrypting this set before sending to destination services.",
                    "category": {
                        "name": "security",
                        "title": "Security",
                        "description": "",
                        "color": "#740C29"
                    },
                    "configuration": {}
                }
            ]
        },
        {
            "name": "JSON",
            "title": "JSON",
            "description": "Prepare for facade",
            "policies": [
                {
                    "name": "ldap",
                    "title": "LDAP",
                    "description": "Encrypting this set before sending to destination services.",
                    "category": {
                        "name": "security",
                        "title": "Security",
                        "description": "",
                        "color": "#740C29"
                    },
                    "configuration": {}
                }
            ]
        }
    ];
    clickPolicy( policyData );
    policyViewer.oUI.emit(policyViewer.EVENT_TYPE_TEMPLATE_READY, 'overview');
});

policyViewer.oUI.on(policyViewer.EVENT_TYPE_TEMPLATE_SHOW, function() {
    //var policyData = policyViewer.oData.getOverview();
    var policyData = [
        {
            "name": "URI",
            "title": "URI",
            "description": "Prepare gateway",
            "policies": [
                {
                    "name": "xml2json",
                    "title": "XML => JSON",
                    "description": "Documentation (optional) Transforming user profile  data to JSON for additional formatting before routing.",
                    "category": {
                        "name": "mapping",
                        "title": "Mapping",
                        "description": "",
                        "color": "#13669A"
                    },
                    "configuration": {}
                },
                {
                    "name": "basic_authentication",
                    "title": "Basic Authentication",
                    "description": "Validate user access before proceeding to further policies.",
                    "category": {
                        "name": "authentication",
                        "title": "Authentication",
                        "description": "",
                        "color": "#EDCC82"
                    },
                    "configuration": {}
                },
                {
                    "name": "ldap",
                    "title": "LDAP",
                    "description": "Encrypting this set before sending to destination services.",
                    "category": {
                        "name": "security",
                        "title": "Security",
                        "description": "",
                        "color": "#740C29"
                    },
                    "configuration": {}
                }
            ]
        },
        {
            "name": "SOAP",
            "title": "SOAP",
            "description": "Prepare for target",
            "policies": [
                {
                    "name": "basic_authentication",
                    "title": "Basic Authentication",
                    "description": "Validate user access before proceeding to further policies.",
                    "category": {
                        "name": "authentication",
                        "title": "Authentication",
                        "description": "",
                        "color": "#EDCC82"
                    },
                    "configuration": {}
                },
                {
                    "name": "ldap",
                    "title": "LDAP",
                    "description": "Encrypting this set before sending to destination services.",
                    "category": {
                        "name": "security",
                        "title": "Security",
                        "description": "",
                        "color": "#740C29"
                    },
                    "configuration": {}
                }
            ]
        },
        {
            "name": "SOAP",
            "title": "SOAP",
            "description": "Prepare for Gateway",
            "policies": [
                {
                    "name": "xml2json",
                    "title": "XML => JSON",
                    "description": "Documentation (optional) Transforming user profile  data to JSON for additional formatting before routing.",
                    "category": {
                        "name": "mapping",
                        "title": "Mapping",
                        "description": "",
                        "color": "#13669A"
                    },
                    "configuration": {}
                },
                {
                    "name": "basic_authentication",
                    "title": "Basic Authentication",
                    "description": "Validate user access before proceeding to further policies.",
                    "category": {
                        "name": "authentication",
                        "title": "Authentication",
                        "description": "",
                        "color": "#EDCC82"
                    },
                    "configuration": {}
                },
                {
                    "name": "ldap",
                    "title": "LDAP",
                    "description": "Encrypting this set before sending to destination services.",
                    "category": {
                        "name": "security",
                        "title": "Security",
                        "description": "",
                        "color": "#740C29"
                    },
                    "configuration": {}
                }
            ]
        },
        {
            "name": "JSON",
            "title": "JSON",
            "description": "Prepare for facade",
            "policies": [
                {
                    "name": "ldap",
                    "title": "LDAP",
                    "description": "Encrypting this set before sending to destination services.",
                    "category": {
                        "name": "security",
                        "title": "Security",
                        "description": "",
                        "color": "#740C29"
                    },
                    "configuration": {}
                }
            ]
        }
    ];
    showPolicy( policyData );
    
    policyViewer.oUI.emit(policyViewer.EVENT_TYPE_TEMPLATE_READY, 'overview');
});

function showPolicy( policyData ){
    var num = policyData.length;
    for( var i = 0; i < num; i++ ){
        var id = '#policy_' + i;
        jQuery(id).children('.book-title').html(policyData[i].title);
        jQuery(id).children('.book-info').html(policyData[i].description);
        var cate = policyData[i].policies.length;

        if ( cate === 0 ) {

            jQuery('.m_'+i).remove();
            jQuery('.s_'+i).remove();
            jQuery('.a_'+i).remove();

        } else if ( cate < 3 ) {

           var tmpStr = 'msa';

           for ( var j = 0; j < cate; j++ ){

               var cls = policyData[i].policies[j].category.name.substr(0,1);
               tmpStr = tmpStr.replace(cls,'');
           }

           var strNum = tmpStr.length;

            for ( var n = 0; n < strNum; n++ ) {

               var k = '.' + tmpStr[n] + '_' + i;
               jQuery(k).remove();
           }
        }
        
    }
}

function clickPolicy( policyData ) {
    jQuery('.book').click(function(){
        var key = jQuery(this).children('input[name=array_key]').val();
        policyViewer.oData.setSelectedNode( policyData[key] );
        policyViewer.oUI.next();
    });
}

