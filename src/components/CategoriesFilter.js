import { ScrollView, StyleSheet, Text, View } from "react-native";
import React from "react";
import { colors } from "../Constants";

const CategoriesFilter = ({ categories }) => {
  return (
    <View>
      <ScrollView horizontal showsHorizontalScrollIndicator={false}>
        {categories.map((categ, index) => {
          return (
            <View
              style={{
                backgroundColor:
                  index === 0 ? colors.COLOR_PRIMARY : colors.COLOR_LIGHT,
                marginRight: 30,
                paddingHorizontal: 16,
                paddingVertical: 10,
                borderRadius: 8,

                shadowColor: "#000",
                shadowOffset: { width: 0, height: 4 },
                shadowRadius: 7,
                shadowOpacity: 0.1,
              }}
              key={categ.id}
            >
              <Text
                style={{
                  color: index === 0 ? colors.COLOR_LIGHT : null,
                  fontSize: 25,
                }}
                // key={categ.id}
              >
                {categ.category}
              </Text>
            </View>
          );
        })}
      </ScrollView>
    </View>
  );
};

export default CategoriesFilter;

const styles = StyleSheet.create({});
