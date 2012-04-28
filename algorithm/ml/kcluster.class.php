<?php

 $centroid ) {
        $centroids[ $i ] = array_sum( $centroid ) / count( $centroid );
    }
    return $centroids;
}

function generate_clusters( $data, $centroids ) {
    // iterate through datapoints and associate each datapoint with the closest centroid

    $max = max( $data ); // Maximum possible distance
    $clusters = array(); // Output
    foreach( $data as $datum ) {
        $closest_dist = $max;
        $index_of_closest = 0;
        foreach( $centroids as $centroid ) {
            $dist = abs( $centroid - $datum );
            //print 'dist between ' . $datum . ' and ' . $centroid . ' is ' . $dist . PHP_EOL;
            if ( $dist < $closest_dist ) {
                // Datapoint was closer to this centroid than the last

                $closest_dist = $dist;
                $index_of_closest = $centroid; // Use centroid value as index to avoid duplicates
            }
        }
        //print 'index of closest is ' . $index_of_closest . PHP_EOL;
        if ( !isset( $clusters[$index_of_closest] ) ) $clusters[$index_of_closest] = array(); // Avoid undefined-offset notices
        array_push( $clusters[$index_of_closest], $datum ); // Store datapoint at index of centroid it's closest to
    }
    return $clusters;
}

// Generate datapoints
$data = array();
for( $i = 0; $i < $DATA_POINT_COUNT; $i++ ) {
    $data[] = mt_rand(0,$i);
}
sort( $data ); // Sort to be neat
print_r( $data );

// Generate initial centroids randomly
$centroids = pick_centroids( $data, $NUM_CLUSTERS );

// Generate new clusters and compare to old ones until there's no change
$old_clusters = 0;      // Seed values
$clusters = !$old_clusters; //
while( $clusters !== $old_clusters ) {
    // Save old clusters
    $old_clusters = $clusters;

    // Generate new ones
    $clusters = generate_clusters( $data, $centroids );

    // Create new centroids
    $centroids = generate_centroids( $clusters );
}

echo 'Clustering complete' . PHP_EOL;
foreach( $clusters as $cluster ) {
    echo 'cluster: ' . implode( ', ', $cluster ) . PHP_EOL;
}