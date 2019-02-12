<!DOCTYPE html>
<html>
    <head>
        <title>New Dashboard</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="http://d3js.org/d3.v3.min.js" language="JavaScript"></script>
        <script src="http://d3js.org/d3.v4.min.js" language="JavaScript"></script>
        <link rel="stylesheet" type="text/css" href="includes/nstyle.css" />
        <style>
            .line {
              fill: none;
              stroke: #6EF1C7;
              stroke-width: 2px;
            }

            .overlay {
              fill: none;
              pointer-events: all;
            }
        </style>
    </head>
    
    <body>
        
        <div class="dashboard">
            <?php include ('includes/header.php'); ?>
            <div class="content">
                <div class="row r1">
                    <div class="col c2">
                        <div>
                            <svg id="voltg" width="985" height="500" style="background-color: #777"></svg>
                        </div>
                        <div style="background-color: #777;">
                            <div id="container">
                                <svg id="powerg"/>
                            </div>
                        </div>
                    </div>
                    <div class="col c1">
                        <div class="row">
                                <div id="tempg"></div>
                        </div>
                        <div class="row">
                             <div id="humg"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="copyright">
            <p>&copy Zero Energy Garden</p>
        </div>
        
        <script>
        temp = 25.6;
        humi = 87;
            
        //power
        var sample = <?php
            require ('mysqli_connect.php');
            $sql="SELECT time, power FROM dashboard ORDER BY no";   //newest at the top
            $query = mysqli_query($dbconnect, $sql);
            if ( ! $query ) {
                echo mysqli_error();
                die;
            }
            $data = array();
            for ($x = 0; $x < mysqli_num_rows($query); $x++) {
                $data[] = mysqli_fetch_assoc($query);
            }
            echo json_encode($data);
            mysqli_close($dbconnect); ?>

        var svg = d3.select('#powerg');
        var svgContainer = d3.select('#container');
    
        var margin = 80;
        var width = 650 - 2 * margin;
        var height = 450 - 1.5 * margin;

        var chart = svg.append('g')
            .attr('transform', `translate(${margin}, ${margin})`);

        var xScale = d3.scaleBand()
            .range([0, width])
            .domain(sample.map((s) => s.time))
            .padding(0.4)

        var yScale = d3.scaleLinear()
            .range([height, 0])
            .domain([0, 20]);

        chart.append('g')
            .attr('transform', `translate(0, ${height})`)
            .attr("class", "axisW")
            .call(d3.axisBottom(xScale));

        chart.append('g')
            .attr("class", "axisW")
            .call(d3.axisLeft(yScale));

        const barGroups = chart.selectAll()
            .data(sample)
            .enter()
            .append('g')

        barGroups
            .append('rect')
            .attr('class', 'powerbar')
            .attr('x', (g) => xScale(g.time))
            .attr('y', (g) => yScale(g.power))
            .attr('height', (g) => height - yScale(g.power))
            .attr('width', xScale.bandwidth())
            .on('mouseenter', function (actual, i) {
            
        d3.selectAll('.bar')
            .attr('opacity', 0.7);

        d3.select(this)
            .attr('opacity', 1);

        const y = yScale(actual.power);

        line = chart.append('line')
            .attr('id', 'limit')
            .attr('x1', 0)
            .attr('y1', y)
            .attr('x2', width)
            .attr('y2', y);

        barGroups.append('text')
            .attr('class', 'divergence')
            .attr('x', (a) => xScale(a.time) + xScale.bandwidth() / 2)
            .attr('y', (a) => yScale(a.power) - 10)
            .attr('fill', 'white')
            .attr('text-anchor', 'middle')
                .text((a, idx) => {
                    let text = '';
                    text += a.power + `W`;

                    return idx == i ? text : '';
                });
          })
          //.text((a) => `${a.value}%`)
          
            .on('mouseleave', function () {
                d3.selectAll('.bar')
                    .attr('opacity', 1);

                d3.select(this)
                    .attr('opacity', 1)
                    .attr('x', (a) => xScale(a.time))
                    .attr('width', xScale.bandwidth());

            chart.selectAll('#limit').remove();
            chart.selectAll('.divergence').remove();
            })
            
        svg
            .append('text')
            .attr('class', 'title')
            .attr('x', width / 2 + 70)
            .attr('y', 40)
            .attr('text-anchor', 'middle')
            .attr('font-weight', 'bold')
            .style('font-size', '14pt')
            .style('text-decoration', 'underline')
            .style('fill', 'white')
            .text('Power Generated')
    
    
        svg
            .append('text')
            .attr('class', 'label')
            .attr('x', -(height / 2) - margin)
            .attr('y', margin / 2)
            .attr('transform', 'rotate(-90)')
            .attr('text-anchor', 'middle')
            .style('fill', 'white')
            .text('Power (W)');
            
        //temperature
        var tempset = {
            chart: {
                height: 400,
                type: 'radialBar',
            },
            series: [temp*2.5],
            colors: ['#57A0D2'],
            labels: ['Temperature'],
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    dataLabels: {
                        name: {
                            fontSize: '18px',
                            color: '#fff',
                            offsetY: 115
                        },
                        value: {
                            offsetY: 76,
                            fontSize: '22px',
                            color: '#fff',
                            formatter: function (tem) {
                                return tem/2.5 + "\xB0C"; //\xBO is degree symbol
                            }
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    type: 'horizontal',
                    shade: 'dark',
                    gradientToColors: ['#EA203F'],  
                    opacityFrom: 0.9,
                    opacityTo: 0.9,
                    stops: [0, 100]
                }
            }
        };

        var temp_chart = new ApexCharts(
            document.querySelector("#tempg"),
            tempset
        );
        
        temp_chart.render();
        
        //humidity
        var humset = {
            chart: {
                height: 400,
                type: 'radialBar'
            },
            series: [humi],
            colors: ['#73C2FB'],
            labels: ['Humidity'],
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    dataLabels: {
                        name: {
                            fontSize: '18px',
                            color: '#fff',
                            offsetY: 115
                        },
                        value: {
                            offsetY: 76,
                            fontSize: '22px',
                            color: '#fff',
                            formatter: function (hum) {
                                return hum + "%";
                            }
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    type: 'horizontal',
                    shade: 'dark',
                    gradientToColors: ['#29AB87'],  
                    opacityFrom: 0.9,
                    opacityTo: 0.9,
                    stops: [0, 100]
                }
            }
        };

        var hum_chart = new ApexCharts(
            document.querySelector("#humg"),
            humset
        );
        
        hum_chart.render();
        
    </script>
    
    <script>
                var svg = d3.select("#voltg"),
                margin = {top: 50, right: 140, bottom: 100, left: 50},
                width = +svg.attr("width") - margin.left - margin.right,
                height = +svg.attr("height") - margin.top - margin.bottom;

            var parseTime = d3.timeParse("%H:%M:%S")
                bisectDate = d3.bisector(function(d) { return d.time; }).left;

            var x = d3.scaleTime().range([0, width]);
            var y = d3.scaleLinear().range([height, 0]);

            var line = d3.line()
                .x(function(d) { return x(d.time); })
                .y(function(d) { return y(d.voltage); });

            var g = svg.append("g")
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            d3.json("data.json", function(error, data) {
                if (error) throw error;

                data.forEach(function(d) {
                  d.time = parseTime(d.time);
                  d.voltage = +d.voltage;
                });

                x.domain(d3.extent(data, function(d) { return d.time; }));
                y.domain([d3.min(data, function(d) { return d.voltage; }) / 1.005, d3.max(data, function(d) { return d.voltage; }) * 1.005]);

                g.append("g")
                    .attr("class", "axis axis--x")
                    .attr("class", "axisW")
                    .attr("transform", "translate(0," + height + ")")
                    .call(d3.axisBottom(x).tickFormat(d3.time.format('%H:%M:%S')));

                g.append("g")
                    .attr("class", "axis axis--y")
                    .attr("class", "axisW")
                    .call(d3.axisLeft(y).ticks(4).tickFormat(function(d) { return parseInt(d); }))
                  .append("text")
                    .attr("class", "axis-title")
                    .attr("transform", "rotate(-90)")
                    .attr("x", -175)
                    .attr("y", -45)
                    .attr("dy", ".71em")
                    .style("text-anchor", "middle")
                    .text("Voltage (V)")
                    .attr("font-size", "1.2em");
            
                g.append('text')
                    .attr('class', 'title')
                    .attr('x', width / 2)
                    .attr('y', -15)
                    .attr('text-anchor', 'middle')
                    .attr('font-weight', 'bold')
                    .style('font-size', '14pt')
                    .style('text-decoration', 'underline')
                    .style('fill', 'white')
                    .text('Voltage-Time Graph')

                g.append("path")
                    .datum(data)
                    .attr("class", "line")
                    .attr("d", line);

                var focus = g.append("g")
                    .attr("class", "focus")
                    .style("display", "none");

                focus.append("line")
                    .attr("class", "x-hover-line hover-line")
                    .attr("y1", 0)
                    .attr("y2", height);

                focus.append("line")
                    .attr("class", "y-hover-line hover-line")
                    .attr("x1", width)
                    .attr("x2", width);

                focus.append("circle")
                    .attr("r", 5)
                    .style('fill', 'white');

                focus.append("text")
                    .attr("x", 15)
                    .attr("dy", ".31em")
                    .style('fill', 'white');

                svg.append("rect")
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
                    .attr("class", "overlay")
                    .attr("width", width)
                    .attr("height", height)
                    .on("mouseover", function() { focus.style("display", null); })
                    .on("mouseout", function() { focus.style("display", "none"); })
                    .on("mousemove", mousemove);
            
                function mousemove() {
                  var x0 = x.invert(d3.mouse(this)[0]),
                      i = bisectDate(data, x0, 1),
                      d0 = data[i - 1],
                      d1 = data[i],
                      d = x0 - d0.time > d1.time - x0 ? d1 : d0;
                    focus.attr("transform", "translate(" + x(d.time) + "," + y(d.voltage) + ")");
                    focus.select("text").text(function() {
                        vtext = d.voltage + "V [" + d3.time.format("%H:%M:%S")(d.time) + "]";
                        return vtext; 
                  });
                  focus.select(".x-hover-line").attr("y2", height - y(d.voltage));
                  focus.select(".y-hover-line").attr("x2", width + width);
                }
            });
            </script>
    
    </body>
</html>
