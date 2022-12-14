<div class="advancedSearch">
    <table class="searchPlayerTableInput mt-5 mb-5">
        <tr>
            <td><input type="text" id="email" placeholder="nhập email"></td>
            <td><input type="text" id="name" placeholder="nhập tên"></td>
            <td><input type="text" id="share" placeholder="đã gửi (0=không hoặc 1=có)"></td>
            <td><input type="text" id="openned_mail" placeholder="đã xem (0 hoặc 1)"></td>
            <td><input type="text" id="downloaded" placeholder="đã tải (0 hoặc 1)"></td>
            <td><input type="button" value="Lọc" id="filterButton"></td>
        </tr>
    </table>
</div>
<script>
    $(document).ready(function () {
        $('#filterButton').click(function () {
            let email = encodeURI($('#email').val());
            let name = encodeURI($('#name').val());
            let share = encodeURI($('#share').val());
            let openned_mail = encodeURI($('#openned_mail').val());
            let downloaded = encodeURI($('#downloaded').val());
            let url = '/document-sharing/manage/user/filter';
            let data = {
                email,
                name,
                share,
                openned_mail,
                downloaded
            };
            $.ajax({
                url,
                type: 'GET',
                data: { data },
                async: true,
            })
                .done(function (data) {
                    console.log('khoa', data)
                    $('#filterUserTable').html(data);
                })
                .fail(function (jqXHR, data) {
                    console.log('khoa', data, jqXHR)
                    alert(jqXHR.responseJSON.message);
                });
        });
    });
</script>

<div id=filterUserTable>
    <?php
    $result = "<table class='table table-hover'>
<tr>
    <th scope='col'>Email</th>
    <th scope='col'>Tên</th>
    <th scope='col'>Đã gửi</th>
    <th scope='col'>Đã xem</th>
    <th scope='col'>Đã tải</th>
    <th >Chi tiết</th>
</tr>";
    foreach ($listUser as $u) {
        $sendTime = $u['send_time'];
        $opennedMailTime = $u['openned_mail_time'];
        $downloadedTime = $u['downloaded_time'];

        // if ((!empty($u['share']) && !empty($u['share']['send_time']))) {
        //     $sendTime = $u['share']['send_time'];
        // }
        // if ((!empty($u['share']) && !empty($u['share']['openned_mail_time']))) {
        //     $opennedMailTime = $u['share']['openned_mail_time'];
        // }
        // if ((!empty($u['share']) && !empty($u['share']['downloaded_time']))) {
        //     $downloadedTime = $u['share']['downloaded_time'];
        // }
    
        $result = $result . "<tr>
        <td>" .
            $u['email']
            . "</td>
        <td>" .
            $u['name']
            . "</td>
        <td>" .
            $sendTime
            . "</td>
        <td>" .
            $opennedMailTime
            . "</td>
        <td>" .
            $downloadedTime
            . "</td>
        <td>";
        $result .= "<a href='/document-sharing/user/" . (string) $u['_id']
            . "'>xem</a></td></tr>";
    }
    $result .= "</table>";
    echo $result;

    ?>

</div>


<div id="my-grid"></div>

<script>
    let listUser = <?= json_encode($listUser) ?>;

    // Target the div element by using jQuery and then call the kendoGrid() method.
    $("#my-grid").kendoGrid({
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

                }
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
        ],
        //toolbar: ["create", "save"],
        filterable: {
            mode: "row", //"menu, row"
        },
        // filterable: true,
        
        pageable: {
            pageSizes: [2, 3, 5, 10],
            alwaysVisible: true,
            //position: 'top',
        },
        sortable: true,
        editable: true,
        dataSource: {
            data: listUser,
            // type: 'json',
            transport: {
                read: "/document-sharing/manage/user/filter",
                parameterMap: function (data) {
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

</script>