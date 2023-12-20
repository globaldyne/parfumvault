/*The MIT License (MIT)

Copyright (c) 2018 https://github.com/FuriosoJack/TableHTMLExport

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.*/

(function($){



    $.fn.extend({
        tableHTMLExport: function(options) {

            var defaults = {
                separator: ',',
                newline: '\r\n',
                ignoreColumns: '',
                ignoreRows: '',
                type:'csv',
                htmlContent: false,
                consoleLog: false,
                trimContent: true,
                quoteFields: true,
                filename: 'tableHTMLExport.csv',
                utf8BOM: true,
                orientation: 'p',
				cover: '',
				maintitle: '',
				subtitle: '',
				product: 'Perfumer\'s Vault Pro',
				coverFontSize: 10,
				titleFontSize: 18,
			    fontSize: 10,
				imgData: '',
				watermarkText: '',
				watermarkTextSize: 200,
            };
            var options = $.extend(defaults, options);


            function quote(text) {
                return '"' + text.replace('"', '""') + '"';
            }


            function parseString(data){

                if(defaults.htmlContent){
                    content_data = data.html().trim();
                }else{
                    content_data = data.text().trim();
                }
                return content_data;
            }

            function download(filename, text) {
                var element = document.createElement('a');
                element.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(text));
                element.setAttribute('download', filename);

                element.style.display = 'none';
                document.body.appendChild(element);

                element.click();

                document.body.removeChild(element);
            }

            /**
             * Convierte la tabla enviada a json
             * @param el
             * @returns {{header: *, data: Array}}
             */
            function toJson(el){

                var jsonHeaderArray = [];
                $(el).find('thead').find('tr').not(options.ignoreRows).each(function() {
                    var tdData ="";
                    var jsonArrayTd = [];

                    $(this).find('th').not(options.ignoreColumns).each(function(index,data) {
                        if ($(this).css('display') != 'none'){
                            jsonArrayTd.push(parseString($(this)));
                        }
                    });
                    jsonHeaderArray.push(jsonArrayTd);

                });

                var jsonArray = [];
                $(el).find('tbody').find('tr').not(options.ignoreRows).each(function() {
                    var tdData ="";
                    var jsonArrayTd = [];

                    $(this).find('td').not(options.ignoreColumns).each(function(index,data) {
                        if ($(this).css('display') != 'none'){
                            jsonArrayTd.push(parseString($(this)));
                        }
                    });
                    jsonArray.push(jsonArrayTd);

                });


                return {header:jsonHeaderArray[0],data:jsonArray};
            }


            /**
             * Convierte la tabla enviada a csv o texto
             * @param table
             * @returns {string}
             */
            function toCsv(table){
                var output = "";
                
                if (options.utf8BOM === true) {                
                    output += '\ufeff';
                }

                var rows = table.find('tr').not(options.ignoreRows);

                var numCols = rows.first().find("td,th").not(options.ignoreColumns).length;

                rows.each(function() {
                    $(this).find("td,th").not(options.ignoreColumns)
                        .each(function(i, col) {
                            var column = $(col);

                            // Strip whitespaces
                            var content = options.trimContent ? $.trim(column.text()) : column.text();

                            output += options.quoteFields ? quote(content) : content;
                            if(i !== numCols-1) {
                                output += options.separator;
                            } else {
                                output += options.newline;
                            }
                        });
                });

                return output;
            }
			
			//ADD A WATERMARK
			function addWaterMark(doc) {
				var totalPages = doc.internal.getNumberOfPages();
				var pageHeight = doc.internal.pageSize.height || doc.internal.pageSize.getHeight();
				var pageWidth = doc.internal.pageSize.width || doc.internal.pageSize.getWidth();


			  for (i = 1; i <= totalPages; i++) {
				doc.setPage(i);
				if(options.imgData){
					doc.addImage(options.imgData, 'PNG', 40, 40, 75, 75);
				}
				doc.saveGraphicsState();
				doc.setGState(new doc.GState({opacity: 0.3}));
				doc.setFontSize(options.watermarkTextSize - options.watermarkTextSize.length)
				doc.text(options.watermarkText,  390 - options.watermarkTextSize.length, 800  , {align: 'center', angle: 45})
				doc.restoreGraphicsState();

			  }
			
			  return doc;
			}
			
            var el = this;
            var dataMe;
            if(options.type == 'csv' || options.type == 'txt'){


                var table = this.filter('table'); // TODO use $.each

                if(table.length <= 0){
                    throw new Error('tableHTMLExport must be called on a <table> element')
                }

                if(table.length > 1){
                    throw new Error('converting multiple table elements at once is not supported yet')
                }

                dataMe = toCsv(table);

                if(defaults.consoleLog){
                    console.log(dataMe);
                }

                download(options.filename,dataMe);


                //var base64data = "base64," + $.base64.encode(tdData);
                //window.open('data:application/'+defaults.type+';filename=exportData;' + base64data);
            }else if(options.type == 'json'){

                var jsonExportArray = toJson(el);

                if(defaults.consoleLog){
                    console.log(JSON.stringify(jsonExportArray));
                }
                dataMe = JSON.stringify(jsonExportArray);

                download(options.filename,dataMe)
                /*
                var base64data = "base64," + $.base64.encode(JSON.stringify(jsonExportArray));
                window.open('data:application/json;filename=exportData;' + base64data);*/
            }else if(options.type == 'pdf'){

                var jsonExportArray = toJson(el);

                var contentJsPdf = {
                    head: [jsonExportArray.header],
                    body: jsonExportArray.data
                };

                if(defaults.consoleLog){
                    console.log(contentJsPdf);
                }

                var doc = new jsPDF(defaults.orientation, 'pt');
				doc.setFontSize(options.fontSize);
				if(defaults.cover){
					doc.setFont('helvetica', 'regular')
					doc.setFontSize(options.coverFontSize)
					doc.text(atob(defaults.cover), 40, 100)
					doc.addPage()
				}
				const addFooters = doc => {
				  const pageCount = doc.internal.getNumberOfPages()
					  for (var i = 1; i <= pageCount; i++) {
						doc.setPage(i)
						doc.setFont('helvetica', 'bold')
						doc.setFontSize(options.titleFontSize)
						doc.text(defaults.maintitle, 38, 20)
						doc.setFontSize(10)
						doc.text(defaults.subtitle, 38, 36)
					  }
					  for (var i = 1; i <= pageCount; i++) {
						doc.setPage(i)
						doc.setFont('helvetica', 'italic')
						doc.setFontSize(8)
						doc.text('Page ' + String(i) + ' of ' + String(pageCount) + '\n' + options.product, doc.internal.pageSize.width / 2, 830, {
						  align: 'center'
						})
					  }
				}
				
				doc.autoTable(contentJsPdf);
				addFooters(doc);
				
				doc = addWaterMark(doc);
                doc.save(options.filename);

            }
            return this;
        }
    });
})(jQuery);

