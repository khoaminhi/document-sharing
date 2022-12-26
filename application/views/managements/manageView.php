<div id="my-grid"></div>

<script>
    let listUser = <?= json_encode($listUser) ?>;

    // Target the div element by using jQuery and then call the kendoGrid() method.
    let grid = $("#my-grid").kendoGrid({
        height: "300px",
        columns: [
            {
                field: "email", title: "Email",
                filterable: {
                    //mode: "row",
                    cell: {
                        // enabled: true,
                        delay: 1500,
                        operator: "contains",
                        suggestionOperator: "contains"
                    },

                },
                template: '<a href="/document-sharing/user/#: _id#">#: email#</a>'
            },
            {
                field: "name", title: "Tên",
                filterable: {
                    mode: "row",
                    cell: {
                        // enabled: true,
                        delay: 1500,
                        operator: "contains",
                        suggestionOperator: "contains"
                    },

                }
            },
            {
                field: "send_time", title: "Đã gửi",
                filterable: {
                    enable: false,
                    cell: {
                        delay: 500,
                        template: sendTimeColumnTemplate,
                    }
                }
            },
            {
                field: "openned_mail_time", title: "Đã xem",
                filterable: {
                    enable: false,
                    cell: {
                        delay: 500,
                        template: sendTimeColumnTemplate,
                    }
                }
            },
            {
                field: "downloaded_time", title: "Đã tải",
                filterable: {
                    enable: false,
                    cell: {
                        delay: 500,
                        template: sendTimeColumnTemplate,
                    }
                }
            },
            { command: ["edit", "destroy"], title: "&nbsp;", width: "250px" },
        ],
        //toolbar: ["create", "save"],
        filterable: {
            mode: "row", //"menu, row"
        },
        // filterable: true,

        pageable: {
            pageSizes: [1, 2, 3, 5, 10,],
            alwaysVisible: true,
            currentPage: 5,
            //position: 'top',
        },
        sortable: true,
        editable: 'popup',
        dataSource: {
            data: listUser,
            // type: 'json',
            transport: {
                read: "/document-sharing/manage/user/filter",
                parameterMap: function (data, type) {
                    if (type === 'read') {
                        if (data?.filter?.filters) {
                            data.filter['data'] = {};
                            data.filter.filters.forEach(element => {
                                data.filter.data[element.field] = element.value;
                            });

                        }
                        console.log(data)
                        return data;
                    }
                },
                update: {
                    url: function(data, another) {
                        console.log('update', data);
                        console.log('another', another); //undefine
                        return `/document-sharing/user/${data._id}`;
                    },
                    dataType: 'json',
                    type: 'GET',
                }
            },
            schema: {
                total: function () {
                    return listUser[0]?.totalDocument || 0;
                },
                model: {
                    id: "_id", // The ID field is a unique identifier that allows the dataSource to distinguish different elements.
                    fields: {
                        email: { type: "string", editable: false }, // The ID field in this case is a number. Additionally, do not allow users to edit this field.
                        name: { type: "string" },
                        send_time: { type: 'string' },
                        openned_mail: { type: "string" },
                        downloaded: { type: "string" },
                    }
                }
            },
            serverPaging: true,
            pageSize: 2,
            currentPage: 3,
            // page: 3,
            serverFiltering: true,
            serverSorting: false,
            // serverGrouping: true,
            // group: { field: "category", dir: "desc" }
        }
    });

    function sendTimeColumnTemplate(args) {
        args.element.kendoDropDownList({
            dataSource: [{ value: '1', text: "Đã" }, { value: '0', text: "Chưa" }],
            optionLabel: "--Chọn--",
            dataTextField: "text",
            dataValueField: "value",
            valuePrimitive: true,
        });
    }

    $(".k-grid-add2", grid.element).bind("click", function (ev) {
        console.log("adding!");
        if (grid.dataSource.data().length < 5) {
            grid.addRow();
        } else {
            alert("Too many, sorry!")
        }
    });

</script>